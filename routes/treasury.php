<?php

    Route::post('eth-transfer',
        'Api\TreasuryController@ethTransferCallback')->name('api.treasury.eth-transfer.callback');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('transactions/{transactionId}',
            'Api\TreasuryController@transactionCallback')->name('api.treasury.transaction.callback');
        Route::post('token-transfer', 'Api\TreasuryController@tokenCallback')->name('api.treasury.token.callback');
        Route::post('balance-callback',
            'Api\TreasuryController@balanceCallback')->name('api.treasury.balance.callback');
    });
