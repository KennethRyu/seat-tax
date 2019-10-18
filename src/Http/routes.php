<?php

Route::group([
    'namespace'  => 'Ryu\Seat\Tax\Http\Controllers',
    'prefix'     => 'seat_tax',
    'middleware' => ['web', 'auth', 'bouncer:seat_tax.view'],
], function () {

    // 账单------------------------------------------------------------------------------------
    Route::get('/', [
        'as'   => 'seat_tax.index',
        'uses' => 'TaxController@index',
    ]);



    Route::get('/alliance/{alliance_id}', [
        'as'   => 'seat_tax.allianceview',
        'uses' => 'TaxController@getLiveBillingView',
    ]);



    Route::get('/getindbilling/{id}', [
        'as'   => 'seat_tax.getindbilling',
        'uses' => 'TaxController@getUserBilling',
    ]);

    Route::get('/pastbilling/{year}/{month}', [
        'as'   => 'seat_tax.pastbilling',
        'uses' => 'TaxController@previousBillingCycle',
    ]);

    Route::get('/getindpastbilling/{id}/{year}/{month}', [
        'as'   => 'seat_tax.getindbilling',
        'uses' => 'TaxController@getPastUserBilling',
    ]);

    // 设置------------------------------------------------------------------------------------
    Route::get('/settings', [
        'as'   => 'seat_tax.settings',
        'uses' => 'SettingsController@index',
    ]);

    Route::post('/settings', [
        'as'   => 'seat_tax.savesettings',
        'uses' => 'SettingsController@save',
    ]);

    // 关于------------------------------------------------------------------------------------
    Route::get('/about', [
        'as'   => 'seat_tax.about',
        'uses' => 'AboutController@index',
    ]);


});
