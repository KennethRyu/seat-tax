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
use Denngarr\Seat\Billing\Helpers\BillingHelper;

class TaxController extends Controller
{
    use MiningLedger, Ledger, CharacterLedger, BillingHelper;

    public function index(int $alliance_id = 0)
    {

        $start_date = carbon()->startOfMonth()->toDateString();
        $end_date = carbon()->endOfMonth()->toDateString();
        DB::connection()->enableQueryLog();
        // 公司成员跟踪
        $mining_stats = DB::table('corporation_member_trackings')
            ->select('corporation_id')
            ->leftJoin('character_minings', 'corporation_member_trackings.character_id', '=', 'character_minings.character_id')
            ->whereBetween('date', [$start_date, $end_date])
            ->groupBy('corporation_id');

        if (setting("pricevalue", true) == "m") {
            $mining_stats = $mining_stats->selectRaw('SUM((character_minings.quantity / 100) * (invTypeMaterials.quantity * (? / 100)) * adjusted_price) as mining', [(float) setting("refinerate", true)])
                ->leftJoin('invTypeMaterials', 'character_minings.type_id', '=', 'invTypeMaterials.typeID')
                ->leftJoin('market_prices', 'invTypeMaterials.materialTypeID', '=', 'market_prices.type_id');
        } else {
            $mining_stats = $mining_stats->selectRaw('SUM(character_minings.quantity * market_prices.average_price) as mining')
                ->leftJoin('market_prices', 'character_minings.type_id', '=', 'market_prices.type_id');
        }

        // 公司钱包日记
        $bounty_stats = DB::table('corporation_wallet_journals')
            ->select('corporation_infos.corporation_id')
            ->selectRaw('SUM(amount) / tax_rate as bounties')
            ->join('corporation_infos', 'corporation_wallet_journals.corporation_id', '=', 'corporation_infos.corporation_id')
            ->whereIn('ref_type', ['bounty_prizes', 'bounty_prize'])
            ->whereBetween('date', [$start_date, $end_date])
            ->groupBy('corporation_id');

        // 公司信息
        $stats = DB::table('corporation_infos')
            ->select('corporation_infos.corporation_id', 'corporation_infos.alliance_id', 'corporation_infos.name', 'corporation_infos.tax_rate', 'mining', 'bounties')
            ->selectRaw('COUNT(corporation_member_trackings.character_id) as members')
            ->selectRaw('COUNT(refresh_tokens.character_id) as actives')
            ->join('corporation_member_trackings', 'corporation_infos.corporation_id', '=', 'corporation_member_trackings.corporation_id')
            ->leftJoin('users', 'corporation_member_trackings.character_id', '=', 'users.id')
            ->leftJoin('refresh_tokens', function ($join) {
                $join->on('users.id', '=', 'refresh_tokens.character_id')
                     ->whereNull('deleted_at');
            })
            ->leftJoin(DB::raw('(' . $mining_stats->toSql() . ') mining_stats'), function($join) {
                $join->on('corporation_infos.corporation_id', '=', 'mining_stats.corporation_id');
            })
            ->leftJoin(DB::raw('(' . $bounty_stats->toSql() . ') bounty_stats'), function ($join) {
                $join->on('corporation_infos.corporation_id', '=', 'bounty_stats.corporation_id');
            })
            ->mergeBindings($mining_stats)
            ->mergeBindings($bounty_stats)
            ->groupBy('corporation_id', 'alliance_id', 'name', 'tax_rate', 'mining', 'bounties')
            ->orderBy('name');

        if ($alliance_id !== 0)
            $stats->where('alliance_id', $alliance_id);

        // 统计数据
        $stats = $stats->get();
        // 联盟
        $alliances = Alliance::whereIn('alliance_id', CorporationInfo::select('alliance_id'))->orderBy('name')->get();

        // 日期
        $dates = $this->getCorporationBillingMonths($stats->pluck('corporation_id')->toArray());

        return view('seat_tax::Tax/summary', compact('alliances', 'stats', 'dates'));

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
