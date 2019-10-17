<?php

Route::group([
    'namespace'  => 'Ryu\Seat\Tax\Http\Controllers',
    'prefix'     => 'seattax',
    'middleware' => ['web', 'auth', 'bouncer:seattax.view'],
], function () {

    Route::get('/', [
        'as'   => 'seattax.view',
        'uses' => 'TaxController@getLiveBillingView',
    ]);

    Route::get('/alliance/{alliance_id}', [
        'as'   => 'seattax.allianceview',
        'uses' => 'TaxController@getLiveBillingView',
    ]);

    Route::get('/settings', [
        'as'   => 'seattax.settings',
        'uses' => 'TaxController@getBillingSettings',
    ]);

    Route::post('/settings', [
        'as'   => 'seattax.savesettings',
        'uses' => 'TaxController@saveBillingSettings',
    ]);

    Route::get('/getindbilling/{id}', [
        'as'   => 'seattax.getindbilling',
        'uses' => 'TaxController@getUserBilling',
    ]);

    Route::get('/pastbilling/{year}/{month}', [
        'as'   => 'seattax.pastbilling',
        'uses' => 'TaxController@previousBillingCycle',
    ]);

    Route::get('/getindpastbilling/{id}/{year}/{month}', [
        'as'   => 'seattax.getindbilling',
        'uses' => 'TaxController@getPastUserBilling',
    ]);

});
