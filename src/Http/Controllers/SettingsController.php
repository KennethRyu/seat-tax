<?php

namespace Ryu\Seat\Tax\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Denngarr\Seat\Billing\Validation\ValidateSettings;

class SettingsController extends Controller
{

    public function index(){
        return view('seat_tax::settings');
    }

    public function save(ValidateSettings $request){

        setting(["oremodifier", $request->oremodifier], true);
        setting(["oretaxrate", $request->oretaxrate], true);
        setting(["refinerate", $request->refinerate], true);
        setting(["bountytaxrate", $request->bountytaxrate], true);
        setting(["ioremodifier", $request->ioremodifier], true);
        setting(["ioretaxrate", $request->ioretaxrate], true);
        setting(["ibountytaxrate", $request->ibountytaxrate], true);
        setting(["irate", $request->irate], true);
        setting(["pricevalue", $request->pricevalue], true);

        return redirect()->back()->with('成功', '设置已成功更新。');
    }


}
