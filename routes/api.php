<?php

\Route::group([
    'middleware' => ['cors'],
    'prefix' => 'api',
], function ($router) {
    Route::get('public/currency/fiatRates', 'ClientApi\CurrencyController@fiatRates')->name('api.currency.fiatRates');
});

\Route::group([
    'middleware' => ['api', 'auth:api-admin', 'set-language-from-partner', 'role:partner'],
    'prefix' => 'api',
], function ($router) {
    Route::get('actions/custom', 'Api\CustomBonusActionsController@index');

    Route::resource('actions', 'Api\ActionsController', [
        'only' => ['index', 'show', 'store', 'update', 'destroy'],
        'names' => [
            'index' => 'api.actions.index',
            'show' => 'api.actions.show',
            'store' => 'api.actions.store',
            'update' => 'api.actions.update',
            'destroy' => 'api.actions.destroy',
        ],
    ]);

    Route::post('rewards', 'Api\RewardsController@store')->name('api.rewards.store');
    Route::put('rewards/{rewardId}', 'Api\RewardsController@update')->name('api.rewards.update');
    Route::delete('rewards/{rewardId}', 'Api\RewardsController@destroy')->name('api.rewards.destroy');

    Route::post('/partner', 'Api\PartnerController@create');

    Route::resource('codes', 'Api\CodesController', [
        'only' => ['index', 'show', 'store', 'update', 'destroy'],
        'names' => [
            'index' => 'api.codes.index',
            'show' => 'api.codes.show',
            'store' => 'api.codes.store',
            'update' => 'api.codes.update',
            'destroy' => 'api.codes.destroy',
        ],
    ]);

    Route::group(['prefix' => 'events'], function () {
        Route::post('/order', 'Api\EventsController@order')->name('api.events.order');
        Route::post('/custom', 'Api\EventsController@custom')->name('api.events.custom');
    });
});
