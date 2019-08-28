<?php

Route::get('offer/action', 'ClientApi\SpecialOfferController@actionList')->name('client.api.offer.actions');
Route::get('offer/reward', 'ClientApi\SpecialOfferController@rewardList')->name('client.api.offer.rewards');
Route::get('translation/reload', 'ClientApi\TranslationController@reload');
Route::get('translation/{locale}', 'ClientApi\TranslationController@localeTranslations');
Route::get('translation', 'ClientApi\TranslationController@localesTranslations');
Route::get('currency/fiatRates', 'ClientApi\CurrencyController@fiatRates')->name('client.api.currency.fiatRates');

Route::get('/action/{action}', 'ClientApi\ActionController@get')
     ->name('client.api.user.action.get');

Route::get('/reward/{reward}', 'ClientApi\RewardController@get')
     ->name('client.api.user.reward.get');

Route::group(['prefix' => '/tempAuth'], function () {
    Route::post('loginByFacebook', 'ClientApi\TempAuthController@loginByFacebook');

    Route::post('checkEmailVerificationCode', 'ClientApi\TempAuthController@checkEmailVerificationCode')

         ->middleware(HApp::isProduction() ? 'throttle:5,5' : 'throttle:20,5');
    Route::post('sendEmailVerificationCode', 'ClientApi\TempAuthController@sendEmailVerificationCode')
         ->middleware(HApp::isProduction() ? 'throttle:5,5' : 'throttle:20,5');
});

Route::group(['middleware' => ['only-authenticated-clients']], function () {
    Route::get('/transaction', 'ClientApi\TransactionController@personTransactionList')
        ->name('client.api.person.transactions');

    Route::get('/coupon', 'ClientApi\CouponsController@personCouponList')
        ->name('client.api.person.coupons');

    Route::get('/wallet', 'ClientApi\WalletController@personWallets')
        ->name('client.api.person.wallets');

    Route::post('/reward/{reward}/acquire', 'ClientApi\RewardController@acquire')
         ->name('client.api.user.reward.acquire');

    Route::post('/action/{action}/perform', 'ClientApi\ActionController@perform')
         ->name('client.api.user.action.perform');

    Route::post('/user/updateBitTokenAddress', 'ClientApi\UserController@updateBitTokenAddress')
        ->name('client.api.user.updateBitTokenAddress');

    Route::post('/user/updateEthereumAddress', 'ClientApi\UserController@updateEthereumAddress')
        ->name('client.api.user.updateEthereumAddress');
});

Route::group(['prefix' => '/{partner}', 'middleware' => ['save-referrer']], function () {
    Route::group(['middleware' => ['autologin-token']], function () {
        Route::get('/', 'ClientApi\PartnerController@get')
            ->name('client.api.partner');

        Route::get('/page', 'ClientApi\PartnerController@mainPage')
            ->name('client.api.index');

        Route::get('/action', 'ClientApi\ActionController@actionList')
            ->name('client.api.partner.actions');

        Route::get('/reward', 'ClientApi\RewardController@getList')
            ->name('client.api.partner.rewards');

        Route::post('/user/emailStatus', 'ClientApi\UserController@emailStatus')
            ->name('client.api.user.email.status');

        Route::post('/user/phoneStatus', 'ClientApi\UserController@phoneStatus')
            ->name('client.api.user.phone.status');

        Route::post('/user/resetPassword', 'ClientApi\UserController@resetPassword')
            ->name('client.api.user.password.reset')
            ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:2,5');

        Route::post('/user/resetPasswordByEmail', 'ClientApi\UserController@resetPasswordByEmail')
            ->name('client.api.user.password.reset.byEmail')
            ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:2,5');

        Route::post('/user/resetPasswordByPhone', 'ClientApi\UserController@resetPasswordByPhone')
            ->name('client.api.user.password.reset.byPhone')
            ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:2,5');

        Route::post('/user/confirmResetPassword', 'ClientApi\UserController@confirmResetPassword')
            ->name('client.api.user.password.reset.confirm')
            ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,10');

        Route::post('/support/feedback', 'ClientApi\SupportController@sendMessageToSupport')
            ->name('client.api.support.feedback');

        Route::get('/support/faq', 'ClientApi\SupportController@faqList')
            ->name('client.api.support.faq');

        //@todo fix correct redirect
        Route::group(['middleware' => ['only-authenticated-clients']], function () {
            Route::get('user/me', 'ClientApi\UserController@me')
                ->name('client.api.user.me');

            Route::get('user/checkEmailIsConfirmed', 'ClientApi\UserController@isEmailConfirmed')
                ->name('client.api.user.email.isConfirmed');

            Route::get('user/sendEmailConfirmation', 'ClientApi\UserController@sendEmailConfirmation')
                ->name('client.api.user.email.sendConfirmation')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,5');

            Route::post('user/confirmEmail', 'ClientApi\UserController@confirmEmail')
                ->name('client.api.user.email.confirm')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,5');

            Route::get('/user/sendPhoneConfirmation', 'ClientApi\UserController@sendPhoneConfirmation')
                ->name('client.api.user.phone.sendConfirmation')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,10');

            Route::post('/user/confirmPhone', 'ClientApi\UserController@confirmPhone')
                ->name('client.api.user.phone.confirm')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,5');

            Route::get('/transaction', 'ClientApi\TransactionController@userTransactionList')
                ->name('client.api.user.transactions');

            Route::get('/coupon', 'ClientApi\CouponsController@userCouponList')
                ->name('client.api.user.coupons');

            Route::get('/wallet', 'ClientApi\WalletController@userWallet')
                ->name('client.api.user.wallet');

            Route::get('user/referralStatistic', 'ClientApi\UserController@referralStatistic')
                ->name('client.api.user.referralStatistic');

            Route::get('/transaction/withdrawHistory', 'ClientApi\TransactionController@withdrawTransactionList')
                ->name('client.api.user.transactions.withdraw');

            Route::post('reward/bitrewardsPayout', 'ClientApi\RewardController@bitrewardsPayout')
                ->name('client.api.reward.bitrewardsPayout');

            Route::get('/transaction/depositHistory', 'ClientApi\TransactionController@depositHistory')
                ->name('client.api.user.transactions.deposit');
        });
    });
});
