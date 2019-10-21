<?php


namespace Ryu\Seat\Tax\Commands;

use Herpaderpaldent\Seat\SeatGroups\Jobs\GroupDispatcher;
use Herpaderpaldent\Seat\SeatGroups\Jobs\GroupSync;
use Illuminate\Console\Command;
use Seat\Web\Models\Group;
use Seat\Web\Models\User;

class TaxRateUpdate extends Command
{

    /**
     * 控制台命令的名称和签名。
     *
     * @var string
     */
    protected $signature = 'tax:update:corporation_rate_change_log';

    protected $description = '
        更新公司税率变动日志
        Update company tax rate change log
    ';

    public function handle()
    {

    }
}
