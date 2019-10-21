<?php


namespace Ryu\Seat\Tax\Commands;

use Illuminate\Console\Command;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;
use Denngarr\Seat\Billing\Models\CharacterBill;
use Denngarr\Seat\Billing\Models\CorporationBill;
use Denngarr\Seat\Billing\Helpers\BillingHelper;

class TaxUpdate extends Command
{

    /**
     * 控制台命令的名称和签名。
     *
     * @var string
     */
    protected $signature = 'tax:update {--F|force} {year?} {month?}';

    protected $description = '军团税收账单，如果不存在，它将记录上个月的账单。';

    public function handle()
    {

        // 上一个月
        $lastmonth = date('Y-n', strtotime('-1 month'));
        list($year, $month) = preg_split("/-/", $lastmonth, 2);

        if (($this->argument('month')) && ($this->argument('year'))) {
            $year = $this->argument('year');
            $month = $this->argument('month');
        }

        if ($this->option('force') == true) {
            CorporationBill::where('month', $month)
                ->where('year', $year)
                ->delete();
            CharacterBill::where('month', $month)
                ->where('year', $year)
                ->delete();
        }
        // See if corps already have a bill.  If not, generate one.
        // 查看军团是否已有账单。 如果没有，请生成一个。

        // 获取所有公司信息
        $corps = CorporationInfo::all();
        foreach ($corps as $corp) {
            $bill = CorporationBill::where('corporation_id', $corp->corporation_id)
                ->where('year', $year)
                ->where('month', $month)
                ->get();


            if ((count($bill) == 0) || ($this->option('force') == true)) {
                if (!$corp->tax_rate) {
                    $corp_taxrate = .10;
                } else {
                    $corp_taxrate = $corp->tax_rate;
                }
                $rates = $this->getCorporateTaxRate($corp->corporation_id);

                $bill = new CorporationBill;
                $bill->corporation_id = $corp->corporation_id;
                $bill->year = $year;
                $bill->month = $month;
                $bill->mining_bill = $this->getMiningTotal($corp->corporation_id, $year, $month);
                $bill->pve_bill = $this->getBountyTotal($corp->corporation_id, $year, $month) / $corp_taxrate;
                $bill->mining_taxrate = $rates['taxrate'];
                $bill->mining_modifier = $rates['modifier'];
                $bill->pve_taxrate = $rates['pve'];
                $bill->save();

                $summary = $this->getMainsBilling($corp->corporation_id, $year, $month);

                foreach ($summary as $character) {
                    $bill = CharacterBill::where('character_id', $character['id'])
                        ->where('year', $year)
                        ->where('month', $month)
                        ->get();
                    if ((count($bill) == 0) || ($this->option('force') == true)) {
                        $bill = new CharacterBill;
                        $bill->character_id = $character['id'];
                        $bill->corporation_id = $corp->corporation_id;
                        $bill->year = $year;
                        $bill->month = $month;
                        $bill->mining_bill = $character['amount'];
                        $bill->mining_taxrate = ($character['taxrate'] * 100);
                        $bill->mining_modifier = $rates['modifier'];
                        $bill->save();
                    }
                }
            }
        }

    }
}
