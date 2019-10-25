<?php

namespace Ryu\Seat\Tax\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;


use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;

use Denngarr\Seat\Billing\Validation\ValidateSettings;

use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Ryu\Seat\Tax\Models\RateChangeModel;
use Ryu\Seat\Tax\Models\CorpBillModel;

use Seat\Eveapi\Models\Corporation\CorporationMember; // 公司员工
use Seat\Eveapi\Models\RefreshToken; // 授权token
use Ryu\Seat\Tax\Helpers\TaxHelper;


class TaxController extends Controller
{
    use TaxHelper;

    protected $begin_time = "";
    protected $end_time   = "";

    public function index(int $alliance_id = 0)
    {

        $this->begin_time = carbon()->startOfMonth();
        $this->end_time   = carbon()->endOfMonth();

        // 获取所有公司信息
        $corps = CorporationInfo::all();

        // 循环获取公司税率信息
        foreach ($corps as &$corp) {

            // 获取当月税率
            $tax = RateChangeModel::where('corporation_id', $corp->corporation_id)
                ->where(function ($query) {

                    $query->where(function ($query) {

                        $query->where(function ($query) {
                            $query->where('begin_time', '>=', $this->begin_time)
                                ->where('begin_time', '<=', $this->end_time);
                        });

                        $query->orWhere(function ($query) {
                            $query->where('end_time', '>=', $this->begin_time)
                                ->where('end_time', '<=', $this->end_time);
                        });
                    });

                    $query->orWhere(function ($query) {

                        $query->where(function ($query) {
                            $query->where('begin_time', '<=', $this->begin_time)
                                ->where('end_time', '>=', $this->end_time);
                        });

                        $query->orWhere(function ($query) {
                            $query->where('begin_time', '<=', $this->begin_time)
                                ->where('end_time', '>=', $this->end_time);
                        });
                    });
                })
                ->orderBy('end_time',"ASC")
                ->get();

            // 循环计算准确的时间段

            $sumTax = 0;
            foreach ($tax as &$val) {

                $begin_time = '';
                $end_time   = '';
                $taxRate = '';
                $whereIndex = 0;

                // 2019-07-24 13:56:00 ~ 2019-08-05 11:56:00
                if( $val['begin_time'] < $this->begin_time && ($val['end_time'] >= $this->begin_time && $val['end_time'] <= $this->end_time) ) {
                    $begin_time = $this->begin_time;
                    $end_time   = $val['end_time'];
                    $whereIndex = 1;
                }
                // 2019-08-05 11:56:00 ~ 2019-08-06 00:52:00
                else if( $val['begin_time'] >= $this->begin_time && ($val['end_time'] >= $this->begin_time && $val['end_time'] <= $this->end_time) ) {
                    $begin_time = $val['begin_time'];
                    $end_time   = $val['end_time'];
                    $whereIndex = 2;
                }
                // 2019-08-06 00:52:00 ~ 2019-10-14 13:31:00
                else if( $val['begin_time'] >= $this->begin_time && $val['end_time'] >= $this->end_time ) {
                    $begin_time = $val['begin_time'];
                    $end_time   = $this->end_time;
                    $whereIndex = 3;
                }
                else if( $val['begin_time'] < $this->begin_time && $val['end_time'] >= $this->end_time ) {
                    $begin_time = $this->begin_time;
                    $end_time   = $this->end_time;
                    $whereIndex = 4;
                }
                // 2019-10-14 13:31:00 ~ NULL(未知)
                else if( $val['begin_time'] < $this->end_time && empty($val['end_time']) ) {
                    $begin_time = $val['begin_time'];
                    $end_time   = $this->end_time;
                    $whereIndex = 5;
                }

                // 当前税率
                $taxRate    = $val['pve_taxrate_new'];

                $whereIn = [
                    'agent_mission_reward',                           // 代理人任务奖励
                    'agent_mission_reward_corporation_tax',           // 代理人任务奖励 军团税金
                    'agent_mission_time_bonus_reward',                // 代理人任务时间加成奖励
                    'agent_mission_time_bonus_reward_corporation_tax',// 代理人任务时间加成奖励 军团税金
                    'bounty_prize',  // 追击赏金
                    'bounty_prizes', // 追击赏金
                    'bounty_prize_corporation_tax', // 追击赏金 军团税金
                    'corporate_reward_payout',      // 军团奖励支付
                    'corporate_reward_tax',         // 军团奖励税
                    'project_discovery_reward',     // 探索计划奖励
                    'project_discovery_tax'         // 针对探索计划奖励的军团税
                ];
                $amount = CorporationWalletJournal::where('date' ,'>=' ,$begin_time)
                    ->where('date', '<=', $end_time)
                    ->where('corporation_id',$corp->corporation_id)
                    ->whereIn('ref_type',$whereIn)
                    ->sum('amount');

//                var_dump('数据记录: ' .$val['begin_time'] .' ~ '. $val['end_time']);
//                var_dump('计算条件: ' . $whereIndex);
//                var_dump('军团钱包 '.$begin_time .' ~ '. $end_time .' : '. $amount);
//                var_dump('税率: '.$taxRate);
//                var_dump('实际产出: ' . $amount / ($taxRate/100) );
//                var_dump('上缴金额: ' . ($amount / ($taxRate/100)) * 0.05 );
//                var_dump('------------------------------------');
                $sumTax += ($amount / ($taxRate/100));
            }

//            var_dump('合计金额: ' . $sumTax );
            $corp->bounties = $sumTax;
//            var_dump('------------------完成------------------');

            // 军团人数
            $corp->members = CorporationMember::where('corporation_id',$corp->corporation_id)->count();
            // 授权人数

            DB::connection()->enableQueryLog();
            $corp->actives = CorporationInfo::where('refresh_tokens.token','!=',NULL)
                            ->leftJoin('corporation_members','corporation_members.corporation_id', '=', 'corporation_infos.corporation_id')
                            ->leftJoin('refresh_tokens','refresh_tokens.character_id', '=', 'corporation_members.character_id')
                            ->count();

//            var_dump($corp->actives);
            $queries = DB::getQueryLog(); // 获取查询日志

//            var_dump($queries); // 即可查看执行的sql，传入的参数等等
        }


//        die;

        // 日期
        $dates = $this->getCorporationBillingMonths($corps->pluck('corporation_id')->toArray());

        return view('seat_tax::Tax/summary', compact( 'corps', 'dates'));

    }

    /**
     * 获取公司
     *
     * @return mixed
     */
    private function getCorporations(){
        if (auth()->user()->hasSuperUser()) {
            $corporations = CorporationInfo::orderBy('name')->get();
        } else {
            $corpids = CharacterInfo::whereIn('character_id', auth()->user()->associatedCharacterIds())
                ->select('corporation_id')
                ->get()
                ->toArray();
            $corporations = CorporationInfo::whereIn('corporation_id', $corpids)->orderBy('name')->get();
        }

        return $corporations;
    }


    /**
     * 获取用户账单
     *
     * @param $corporation_id
     * @return array
     */
    public function getUserBilling($corporation_id){
        $summary = $this->getMainsBilling($corporation_id);
        return $summary;
    }

    /**
     * 获取过去的用户帐单
     *
     * @param $corporation_id
     * @param $year
     * @param $month
     * @return mixed
     */
    public function getPastUserBilling($corporation_id, $year, $month){
        $summary = $this->getPastMainsBillingByMonth($corporation_id, $year, $month);
        return $summary;
    }

    /**
     * 上一个帐单周期
     *
     * @param $year
     * @param $month
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function previousBillingCycle($year, $month){

        $corporations = $this->getCorporations();

        $stats = $this->getCorporationBillByMonth($year, $month)->sortBy('corporation.name');
        $dates = $this->getCorporationBillingMonths($corporations->pluck('corporation_id')->toArray());

        return view('seat_tax::Tax/pastbill', compact('stats', 'dates', 'year', 'month'));
    }
}
