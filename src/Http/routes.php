<?php

Route::group([
    'namespace'  => 'Ryu\Seat\Tax\Http\Controllers',
    'prefix'     => 'seat_tax',
    'middleware' => ['web', 'auth', 'bouncer:seat_tax.view'],
], function () {

    Route::get('/', [
        'as'   => 'seat_tax.index',
        'uses' => 'TaxController@index',
    ]);

    Route::get('/', [
        'as'   => 'seat_tax.about',
        'uses' => 'TaxController@about',
    ]);

    Route::get('/alliance/{alliance_id}', [
        'as'   => 'seat_tax.allianceview',
        'uses' => 'TaxController@getLiveBillingView',
    ]);

    Route::get('/settings', [
        'as'   => 'seat_tax.settings',
        'uses' => 'TaxController@getBillingSettings',
    ]);

    Route::post('/settings', [
        'as'   => 'seat_tax.savesettings',
        'uses' => 'TaxController@saveBillingSettings',
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


});
