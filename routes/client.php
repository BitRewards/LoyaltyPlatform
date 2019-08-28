<?php

Route::group(['middleware' => ['save-referrer', 'use-partner-client']], function () {
    Route::group(['middleware' => ['autologin-token']], function () {
        Route::any('/save-email-from-external-request', 'Client\UserController@saveEmailFromExternalRequest')->name('client.saveEmailFromExternalRequest');

        Route::get('/', 'Client\UserController@index')->name('client.index');
        Route::get('/set-cookies', 'Client\UserController@setAppCookies')->name('client.setAppCookies');

        Route::group(['middleware' => ['only-authenticated-clients']], function () {
            Route::get('/unsubscribe', 'Client\UserController@unsubscribe')->name('client.unsubscribe');
            Route::get('logout', 'Client\UserController@logout')->name('client.logout');
            Route::post('checkEmailIsConfirmed', 'Client\UserController@checkEmailIsConfirmed')
                ->name('client.checkEmailIsConfirmed');
            Route::get('confirmEmail', 'Client\UserController@confirmEmail')
                ->name('client.confirmEmail')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,5');
            Route::post('confirmPhone', 'Client\UserController@confirmPhone')
                ->name('client.confirmPhone')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,5');
            Route::post('sendEmailConfirmation', 'Client\UserController@sendEmailConfirmation')
                ->name('client.sendEmailConfirmation')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,5');
            Route::get('sendPhoneConfirmation', 'Client\UserController@sendPhoneConfirmation')
                ->name('client.sendPhoneConfirmation')
                ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,10');

            // Lazy load
            Route::get('getTransactions', 'Client\UserController@getTransactions')->name('client.getTransactions');

            Route::post('reward/bitrewardsPayout', 'Client\RewardController@bitrewardsPayout')->name('client.reward.bitrewardsPayout');
            Route::post('reward/confirmDeposit', 'Client\RewardController@confirmDeposit')->name('client.reward.confirmDeposit');
            Route::post('reward/updateWalletAddress', 'Client\RewardController@updateWalletAddress')->name('client.reward.updateWalletAddress');
            Route::post('reward/updateEthWalletAddress', 'Client\RewardController@updateEthWalletAddress')->name('client.reward.updateEthWalletAddress');
            Route::get('reward/bitrewardsPaidOutModal/{transaction}', 'Client\RewardController@bitrewardsPaidOutModal')
                ->middleware('can:view,transaction')
                ->name('client.reward.bitrewardsPaidOutModal');

            Route::group(['middleware' => 'only-confirmed-clients'], function () {
                Route::post('reward/acquire/{reward}', 'Client\RewardController@acquire')->name('client.reward.acquire');
                Route::get('reward/usageModal/{transaction}', 'Client\RewardController@usageModal')
                    ->middleware('can:view,transaction')
                    ->name('client.reward.usageModal');
                Route::get('coupon/usageModal/{savedCoupon}', 'Client\CouponController@savedCouponModal')
                    ->name('client.savedCoupon.usageModal');
            });

            Route::post('events/process/{action}', 'Client\EventController@process')->name('client.events.process');
            Route::post('events/acquireCode', 'Client\EventController@acquireCode')->name('client.events.acquireCode');

            Route::post('invite', 'Client\UserController@invite')->name('client.invite');

            Route::get('getConfirmationStatus', 'Client\UserController@getConfirmationStatus')
                ->name('client.getConfirmationStatus');

            Route::group(['prefix' => '/person'], function () {
                Route::post('confirmEmail', 'Client\PersonController@confirmEmail')->name('clients.person.confirmEmail');
                Route::post('confirmPhone', 'Client\PersonController@confirmPhone')->name('clients.person.confirmPhone');
                Route::post('addEmail', 'Client\PersonController@addEmail')->name('clients.person.addEmail');
                Route::post('addPhone', 'Client\PersonController@addPhone')->name('clients.person.addPhone');
                Route::post('oauth', 'Client\PersonController@oauth')->name('clients.person.oauth');
            });

            Route::get('referralStatistic', 'Client\UserController@referralStatistic')
                ->name('client.user.referralStatistic');

            Route::post('reward/fiatWithdraw', 'Client\RewardController@fiatWithdraw')
                ->name('client.reward.fiatWithdraw');

            Route::post('action/confirmShare', 'Client\ActionController@confirmShare')
                ->name('client.action.confirmShare');

            Route::post('action/confirmShare/checkTransactionStatus', 'Client\ActionController@checkTransactionStatus')
                ->name('client.action.confirmShare.checkTransactionStatus');
        });
    });

    Route::get('balance/{userKey}/{callback?}', 'Client\UserController@getBalance')
        ->middleware('disable-cookies')
        ->name('client.getBalance');

    Route::get('user/{userKey}/{callback?}', 'Client\UserController@getBasicUserData')
         ->middleware('disable-cookies')
         ->name('client.getBasicUserData');

    Route::post('signupByEmail', 'Client\UserController@signupByEmail')
        ->name('client.signupByEmail')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:8,10');
    Route::post('signupByPhone', 'Client\UserController@signupByPhone')
        ->name('client.signupByPhone')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:8,10');

    Route::group(['prefix' => 'authentication'], function () {
        Route::post('checkEmailStatus', 'Client\AuthenticationController@checkEmailStatus')->name('client.authentication.checkEmailStatus');
        Route::post('checkPhoneStatus', 'Client\AuthenticationController@checkPhoneStatus')->name('client.authentication.checkPhoneStatus');
        Route::post('validateEmail', 'Client\AuthenticationController@validateEmail')->name('client.authentication.validateEmail');
        Route::post('validatePhone', 'Client\AuthenticationController@validatePhone')->name('client.authentication.validatePhone');
        Route::post('setPassword', 'Client\AuthenticationController@setPassword')->name('client.authentication.setPassword');
        Route::post('sendPhoneValidationToken', 'Client\AuthenticationController@sendPhoneValidationToken')->name('client.authentication.sendPhoneValidationToken');
        Route::post('sendEmailValidationToken', 'Client\AuthenticationController@sendEmailValidationToken')->name('client.authentication.sendEmailValidationToken');
        Route::post('login', 'Client\AuthenticationController@login')->name('client.authentication.login');
        Route::post('sendPasswordResetToken', 'Client\AuthenticationController@sendPasswordResetToken')->name('client.authentication.sendPasswordResetToken');
        Route::post('providePhone', 'Client\AuthenticationController@providePhone')->name('client.authentication.providePhone');
        Route::post('provideEmail', 'Client\AuthenticationController@provideEmail')->name('client.authentication.provideEmail');
    });

    Route::post('forgot', 'Client\UserController@forgot')->name('client.forgot')->middleware(HApp::isPhpUnitRunning() || 'local' === App::environment() ? null : 'throttle:2,5');
    Route::post('checkEmail', 'Client\UserController@checkEmail')->name('client.checkEmail');
    Route::post('checkPhone', 'Client\UserController@checkPhone')->name('client.checkPhone');
    Route::post('provideEmail', 'Client\UserController@provideEmail')->name('client.provideEmail');
    Route::post('providePhone', 'Client\UserController@providePhone')->name('client.providePhone');
    Route::get('twitterOAuth', 'Client\UserController@twitterOAuth')->name('client.twitterOAuth');
    Route::get('resetByEmail', 'Client\UserController@resetPasswordByEmail')->name('client.resetByEmailRequest');
    Route::post('resetByPhone', 'Client\UserController@resetPasswordByPhone')
        ->name('client.resetByPhoneRequest')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:5,10');
    Route::post('reset', 'Client\UserController@setNewPassword')->name('client.reset')->middleware(HApp::isPhpUnitRunning() || 'local' === App::environment() ? null : 'throttle:5,10');
    Route::post('mergeByPhone', 'Client\UserController@mergeByPhone')
        ->name('client.mergeByPhone')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:8,10');
    Route::post('sendMergeByPhoneConfirmation', 'Client\UserController@sendMergeByPhoneConfirmation')
        ->name('client.sendMergeByPhoneConfirmation')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,10');
    Route::post('mergeByEmail', 'Client\UserController@mergeByEmail')
        ->name('client.mergeByEmail')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:8,10');
    Route::post('sendMergeByEmailConfirmation', 'Client\UserController@sendMergeByEmailConfirmation')
        ->name('client.sendMergeByEmailConfirmation')
        ->middleware(HApp::isPhpUnitRunning() ? null : 'throttle:3,10');
    Route::post('support', 'Client\UserController@support')->name('client.support');
});
