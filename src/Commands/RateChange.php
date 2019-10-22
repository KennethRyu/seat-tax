<?php


namespace Ryu\Seat\Tax\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Industry\CharacterMining;

use Seat\Eveapi\Models\Corporation\CorporationInfo;//   公司信息
use Seat\Eveapi\Models\Corporation\CorporationMember;// 公司成员

use Seat\Services\Repositories\Character\MiningLedger as CharacterLedger;
use Seat\Services\Repositories\Corporation\Ledger;
use Seat\Services\Repositories\Corporation\MiningLedger;
use Denngarr\Seat\Billing\Models\CharacterBill;
use Denngarr\Seat\Billing\Models\CorporationBill;
use Denngarr\Seat\Billing\Helpers\BillingHelper;
use Ryu\Seat\Tax\Models\RateChangeModel;

class RateChange extends Command
{

    /**
     * 控制台命令的名称和签名。
     *
     * @var string
     */
    protected $signature = 'tax:update:corporation_rate_change_log';

    protected $description = '
        公司税率变动日志
        Update company tax rate change log
    ';

    public function handle() {

        // 获取所有公司信息
        $corps = CorporationInfo::all();

        foreach ($corps as $corp) {

            // 获取该公司下 所有的员工
            $corp_members = DB::table('corporation_members')->where('corporation_id',$corp->corporation_id)->get();

            $corp_members_ids = [];
            foreach ($corp_members as $corp_member) {
                $corp_members_ids[$corp->corporation_id][] = $corp_member->character_id;
            }

            // 获取所有员工的通知
            $where = [];
            $where['type'] = 'CorpTaxChangeMsg';
            $where['sender_type'] = 'character';

            $where_in = [];
            $where_in['character_id'] = $corp_members_ids[$corp->corporation_id];

            $Notices = DB::table('character_notifications')->where($where)
                ->select('character_id','notification_id','timestamp','text')
                ->whereIn('character_id',$corp_members_ids[$corp->corporation_id])
                ->get();

            // 开始处理数据 -> 插入 公司税率变动日志表
            $changeData = [];
            foreach ($Notices as $key => $Notice) {

                $text = explode(" ",$Notice->text);

                $changeData[$key]['corporation_id'] = $corp->corporation_id;
                $changeData[$key]['notification_id'] = $Notice->notification_id;
                $changeData[$key]['pve_taxrate_old'] = floatval($text[2]);
                $changeData[$key]['pve_taxrate_new'] = floatval($text[3]);
                $changeData[$key]['begin_time'] = $Notice->timestamp;
                $changeData[$key]['end_time'] = $key > 0 ? $changeData[$key]['end_time'] = $Notices[$key-1]->timestamp : null;
                $changeData[$key]['created_at'] = date('Y-m-d H:i:s');
                $changeData[$key]['updated_at'] = date('Y-m-d H:i:s');


            }

            RateChangeModel::updateOrCreate($changeData);

        }

    }
}
