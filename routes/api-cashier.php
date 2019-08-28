<?php

\Route::group([
    'middleware' => [
        'administrator-authentication',
        'api',
        'auth:api-admin',
        'set-language-from-partner',
        'role:partner-or-cashier',
        'only-partners',
    ],
    'prefix' => 'api',
], function () {
    Route::get('partner', 'Api\PartnerController@index'); // Old: 'partner.profile'

    Route::get('search/users', 'Api\Search\UsersController@index'); // Old: 'user.search'
    Route::get('coupons/check', 'Api\Search\CouponsController@index'); // Old: 'coupon.find'

    Route::group(['prefix' => 'users'], function () {
        Route::post('/', 'Api\UsersController@store')->name('api.users.store'); // Old: 'user.create'
        Route::post('/bonus', 'Api\UserBonusController@storeExtended')->name('api.users.bonuses.storeExtended');

        Route::group(['prefix' => '{userKey}'], function () {
            Route::get('/', 'Api\UsersController@show')->name('api.users.show'); // Old: 'user.get'
            Route::post('/bonus', 'Api\UserBonusController@store')->name('api.users.bonuses.store'); // Old: 'user.giveBonus'
            Route::post('/cards', 'Api\UserCardsController@store')->name('api.users.cards.store'); // Old: 'user.acquireCode'
            Route::delete('/cards/{cardToken}', 'Api\UserCardsController@destroy')->name('api.users.cards.destroy');
            Route::post('/sms/send', 'Api\UserVerificationController@send'); // Old: 'reward.sendSmsConfirmation'
            Route::post('/sms/verify', 'Api\UserVerificationController@verify'); // Old: 'reward.verifySmsConfirmation'
            Route::get('/transactions', 'Api\UserTransactionsController@index');
        });
    });

    Route::group(['prefix' => 'rewards'], function () {
        Route::get('/', 'Api\RewardsController@index')->name('api.rewards.index'); // Old: 'reward.getAvailable'
        Route::get('/{rewardId}', 'Api\RewardsController@show')->name('api.rewards.show'); // Old: 'reward.get'
        Route::post('{rewardId}/acquire', 'Api\RewardsController@acquire')->name('api.rewards.acquire'); // Old: 'reward.acquire'
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', 'Api\TransactionController@index')->name('api.transactions.index'); // Old: 'transaction.listByUser'
        Route::get('/{transactionId}', 'Api\TransactionController@show')->name('api.transactions.show'); // Old: 'transaction.get'
        Route::post('/{transactionId}/cancelPromoCode', 'Api\TransactionController@cancelPromoCode')->name('api.transactions.cancelPromoCode'); // Old: 'transaction.cancelPromoCode'
    });

    Route::post('coupon/charge', 'Api\CouponController@charge')->name('api.coupons.charge'); // Old: 'coupon.charge'
});
