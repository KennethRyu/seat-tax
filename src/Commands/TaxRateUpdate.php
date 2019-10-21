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
    protected $signature = 'rax_rate:update';

    protected $description = '更新军团税率变动';

    public function handle()
    {

    }
}
