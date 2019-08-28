<?php

use App\Nova\Http\Middleware\ReferralDashboardMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::prefix('referral-tool')
    ->middleware(ReferralDashboardMiddleware::class)
    ->group(static function () {
        Route::get('/cards', 'Bitrewards\ReferralTool\Http\Controllers\CardsController@cards');

        Route::get('/payment/{transaction}/confirm', 'Bitrewards\ReferralTool\Http\Controllers\PaymentController@confirm')
            ->name('referral.payment.confirm');

        Route::get('/payment/{transaction}/reject', 'Bitrewards\ReferralTool\Http\Controllers\PaymentController@reject')
            ->name('referral.payment.reject');

        Route::get('/settings', 'Bitrewards\ReferralTool\Http\Controllers\SettingsController@settings')
            ->name('partner.settings');

        Route::post('/settings', 'Bitrewards\ReferralTool\Http\Controllers\SettingsController@saveSettings')
            ->name('partner.settings.save');
    });

Route::get('/tools-statistic/cards', 'Bitrewards\ReferralTool\Http\Controllers\ToolsStatisticController@getCards');
Route::get('/table/tools-statistic', 'Bitrewards\ReferralTool\Http\Controllers\ToolsStatisticController@toolsStatistic');
