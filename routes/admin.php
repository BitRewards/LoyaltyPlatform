<?php

Route::group(['prefix' => 'admin', 'middleware' => ['set-language-from-partner']], function () {
    Route::group(['middleware' => ['auth:admin', 'role:admin']], function () {
        Route::get('loginAsPartner/{id}', 'Admin\PartnerCrudController@loginAsPartner');
        CRUD::resource('partner', 'Admin\PartnerCrudController', ['parameters' => ['partner' => 'id']]);
        Route::get('partner/{id}/customizations', 'Admin\PartnerCrudController@customizations');
        Route::get('partner/convertRewards/{id}', 'Admin\PartnerCrudController@convertRewards');
        Route::put('partner/{id}/customizations', 'Admin\PartnerCrudController@updateCustomizationSettings');
        HCrud::resource('log', 'Admin\LogCrudController');
        HCrud::resource('specialOfferAction', 'Admin\SpecialOfferActionCrudController', ['ability_entity' => \App\Models\SpecialOfferAction::class]);
        HCrud::resource('specialOfferReward', 'Admin\SpecialOfferRewardCrudController', ['ability_entity' => \App\Models\SpecialOfferReward::class]);
        HCrud::resource('merchant', 'Admin\MerchantCrudController');
        HCrud::resource('referrer', 'Admin\ReferrerCrudController');
        HCrud::resource('referrerWithdraw', 'Admin\ReferrerWithdrawCrudController');
        HCrud::resource('referrerTransaction', 'Admin\ReferrerTransactionCrudController');
        HCrud::resource('partnerDeposit', 'Admin\PartnerDepositCrudController');

        Route::get('referrerWithdraw/confirm/{id}', 'Admin\ReferrerWithdrawCrudController@confirm')->name('admin.referrerWithdraw.confirm');
        Route::get('referrerWithdraw/reject/{id}', 'Admin\ReferrerWithdrawCrudController@reject')->name('admin.referrerWithdraw.reject');
        Route::get('referrerTransaction/confirm/{id}', 'Admin\ReferrerTransactionCrudController@confirm')->name('admin.referrerTransaction.confirm');
        Route::get('referrerTransaction/reject/{id}', 'Admin\ReferrerTransactionCrudController@reject')->name('admin.referrerTransaction.reject');
        Route::get('partnerDeposit/confirm/{id}', 'Admin\PartnerDepositCrudController@confirm')->name('admin.partnerDeposit.confirm');
        Route::get('partnerDeposit/reject/{id}', 'Admin\PartnerDepositCrudController@reject')->name('admin.partnerDeposit.reject');
    });

    Route::group(['middleware' => ['auth:admin', 'role:partner']], function () {
        Route::get('transaction/export', 'Admin\TransactionCrudController@export');
    });

    Route::group(['middleware' => ['auth:admin', 'role:partner']], function () {
        CRUD::resource('transaction', 'Admin\TransactionCrudController', ['parameters' => ['transaction' => 'id']]);
        HCrud::resource('action', 'Admin\ActionCrudController');
        HCrud::resource('reward', 'Admin\RewardCrudController');
        Route::get('user/createBulk', 'Admin\UserCrudController@createBulk')->name('admin.user.createBulk');
        Route::get('createMassAward', 'Admin\RewardCrudController@createMassAward')->name('admin.rewards.createMassAward');
        Route::post('storeMassAward', 'Admin\RewardCrudController@storeMassAward')->name('admin.rewards.storeMassAward');
        Route::post('user/previewBulk', 'Admin\UserCrudController@previewBulk')->name('admin.user.previewBulk');
        Route::post('user/storeBulk', 'Admin\UserCrudController@storeBulk')->name('admin.user.storeBulk');

        Route::get('user/export', 'Admin\UserCrudController@export');
        HCrud::resource('user', 'Admin\UserCrudController');

        HCrud::resource('cashier-users', 'Admin\CashierUsersController', [
            'ability_entity' => 'cashierUser',
            'route_binding' => 'cashierUser',
        ]);
        Route::get('createBulkCodes', 'Admin\CodeCrudController@createBulk')->name('admin.code.createBulk');
        Route::post('storeBulkCodes', 'Admin\CodeCrudController@storeBulk')->name('admin.code.storeBulk');
        HCrud::resource('code', 'Admin\CodeCrudController');
        Route::post('user/giveBonus', 'Admin\UserCrudController@giveBonus')->name('admin.user.giveBonus');
        Route::get('transaction/confirm/{id}', 'Admin\TransactionCrudController@confirm')->name('admin.transaction.confirm');
        Route::get('transaction/reject/{id}', 'Admin\TransactionCrudController@reject')->name('admin.transaction.reject');

        Route::get('transaction/redirectToUser/{id}', 'Admin\TransactionCrudController@redirectToUser')->name('admin.transaction.redirectToUser');

        Route::get('eventbrite/oauth-redirect', 'Admin\EventbriteController@oauthRedirect')->name('admin.eventbrite.oauth-redirect');
        Route::get('eventbrite/oauth-unbind', 'Admin\EventbriteController@unbind')->name('admin.eventbrite.unbind');

        Route::get('/reports', 'Admin\ReportsController@index')->name('admin.reports.index');
        Route::post('/reports', 'Admin\ReportsController@show')->name('admin.reports.show');
        HCrud::resource('help-items', 'Admin\HelpItemsCrudController', [
            'ability_entity' => 'helpItem',
            'route_binding' => 'helpItem',
        ]);

        Route::get('/wallet', 'Admin\WalletController@main')->name('admin.wallet.index');
        Route::get('/wallet/transactions', 'Admin\WalletController@listTransactions')->name('admin.wallet.transactions');
        Route::get('/wallet/get-balance', 'Admin\WalletController@getBalance')->name('admin.wallet.get-balance');
        Route::get('/wallet/get-bit-transfer-fee', 'Admin\WalletController@getTokenTransferEthFeeEstimate')->name('admin.wallet.get-bit-transfer-fee');
        Route::get('/wallet/get-eth-transfer-fee', 'Admin\WalletController@getEthTransferFeeEstimate')->name('admin.wallet.get-eth-transfer-fee');

        Route::get('/wallet/withdraw', 'Admin\WalletController@withdrawForm')->name('admin.wallet.withdrawForm');
        Route::post('/wallet/withdraw', 'Admin\WalletController@withdrawRequest')->name('admin.wallet.withdrawRequest');

        Route::get('/wallet/exchange', 'Admin\WalletController@exchangeForm')->name('admin.wallet.exchangeForm');
        Route::post('/wallet/exchange', 'Admin\WalletController@exchangeRequest')->name('admin.wallet.exchangeRequest');

        Route::get('/bitrewards-settings', 'Admin\WalletController@settingsForm')->name('admin.wallet.settings');
        Route::post('/bitrewards-settings', 'Admin\WalletController@updateSettings')->name('admin.wallet.updateSettings');
    });

    Route::get('/', 'AdminController@redirect')->name('admin');

    Route::get('cashier', 'Admin\CashierController@index')->name('cashier.index');

    Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login')->middleware('guest');
    Route::post('login', 'Auth\LoginController@login')->name('admin.doLogin')->middleware('guest');

    Route::get('loginByToken/{token}', 'Auth\LoginController@loginByApiToken')->name('admin.loginByToken');
    Route::get('logout', 'Auth\LoginController@logout')->name('admin.logout');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
