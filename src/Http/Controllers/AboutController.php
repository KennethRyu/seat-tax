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

use GuzzleHttp\Client;
use Parsedown;

class AboutController extends Controller
{

    public function index()
    {

        $response = (new Client())
            ->request('GET', 'https://raw.githubusercontent.com/herpaderpaldent/seat-groups/master/CHANGELOG.md');
        if ($response->getStatusCode() != 200) {
            return 'Error while fetching changelog';
        }
        $parser = new Parsedown();

        $changelog = $parser->parse($response->getBody());

        return view('seat_tax::about', compact('changelog'));
    }


}
