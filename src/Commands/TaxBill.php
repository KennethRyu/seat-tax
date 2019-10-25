<?php


namespace Ryu\Seat\Tax\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;

use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Ryu\Seat\Tax\Models\RateChangeModel;
use Ryu\Seat\Tax\Models\CorpBillModel;

use Ryu\Seat\Tax\Helpers\TaxHelper;

class TaxBill extends Command
{

    use TaxHelper;

    /**
     * 控制台命令的名称和签名。
     *
     * @var string
     */
    protected $signature = 'tax:update:tax_bill';

    protected $description = '
        公司的税单不存在，上个月的帐单将被记录。
        The company tax bill does not exist and the bill for the previous month will be recorded.
    ';


    protected $begin_time = "";
    protected $end_time   = "";

    public function handle()
    {

        DB::connection()->enableQueryLog();

        // 上一个月开始时间与结束时间

        // ccp时间
        $this->begin_time = date('Y-m-d H:i:s', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));
        $this->end_time   = date('Y-m-d H:i:s', strtotime(date('Y-m', time()) . ' 00:00:00') - 1);

        // 北京时间 -8
        $this->begin_time = date('Y-m-t', strtotime('-2 month')) . ' 16:00:00';
        $this->end_time = date('Y-m-t', strtotime('-1 month')) . ' 15:59:59';

        $lastmonth = date('Y-n', strtotime('-1 month'));
        list($year, $month) = preg_split("/-/", $lastmonth, 2);

//        $this->begin_time = '2019-07-31 16:00:00';
//        $this->end_time = '2019-08-31 15:59:59';

//        $this->begin_time = '2019-09-01 00:00:00';
//        $this->end_time = '2019-09-31 23:59:59';
//
//        $this->begin_time = '2019-10-01 00:00:00';
//        $this->end_time = '2019-10-31 23:59:59';

        // See if corps already have a bill.  If not, generate one.
        // 查看军团是否已有账单。 如果没有，请生成一个。

        // 获取所有公司信息
        $corps = CorporationInfo::all();

        // 循环获取公司税率信息
        foreach ($corps as $corp) {

            $bill = CorpBillModel::where('corporation_id', $corp->corporation_id)
                                ->where('year', $year)
                                ->where('month', $month)
                                ->get();

            // 有数据,跳出循环
            if( count($bill) > 0 ){
                continue;
            }

            // 获取上月税率
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
            foreach ($tax as $val) {

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

//                var_dump($val['begin_time'] > $this->end_time);

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
                $sumTax += ($amount / ($taxRate/100)) * 0.05;
            }

//            var_dump('合计金额: ' . $sumTax );
//            var_dump('------------------完成------------------');
            // 开始计算军团实际税务金额
            $rates = $this->getCorporateTaxRate($corp->corporation_id);

            $bill = new CorpBillModel;
            $bill->corporation_id = $corp->corporation_id;
            $bill->year = $year;
            $bill->month = $month;

            $bill->mining_bill = $this->getMiningTotal($corp->corporation_id, $year, $month);
            $bill->mining_taxrate = $rates['taxrate'];
            $bill->mining_modifier = $rates['modifier'];

            $bill->pve_bill = $sumTax;
            $bill->pve_taxrate = $rates['pve'];
            $bill->save();

        }


        $queries = DB::getQueryLog(); // 获取查询日志

//        print_r($queries); // 即可查看执行的sql，传入的参数等等
    }
}
