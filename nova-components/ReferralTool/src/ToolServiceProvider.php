<?php

namespace Bitrewards\ReferralTool;

use Bitrewards\ReferralTool\Metrics\ReferralConfirmedPurchasedAmountTrend;
use Bitrewards\ReferralTool\Metrics\ReferralConfirmedPurchasedCountTrend;
use Bitrewards\ReferralTool\Metrics\ReferralPurchasedAmountTrend;
use Bitrewards\ReferralTool\Metrics\ReferralPurchasedCountTrend;
use Bitrewards\ReferralTool\Metrics\ReferralsCountTrend;
use Bitrewards\ReferralTool\Metrics\ReferrersEarningTrend;
use Bitrewards\ReferralTool\Metrics\ReferrersValue;
use Bitrewards\ReferralTool\Metrics\ReferrerSystemBalance;
use Bitrewards\ReferralTool\Metrics\ReferrerTrend;
use Laravel\Nova\Metrics\Metric;
use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Bitrewards\ReferralTool\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'referral-tool');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
        });
    }

    /**
     * Register the tool's routes.
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('dashboard')
                ->group(__DIR__.'/../routes/api.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-api/metrics')
            ->group(function () {
                collect(self::cards())
                    ->whereInstanceOf(Metric::class)
                    ->map(function (Metric $metric) {
                        Route::get($metric->uriKey(), 'Bitrewards\\ReferralTool\\Http\\Controllers\\MetricController@show')
                            ->defaults('metric', $metric->uriKey());
                    });
            });
    }

    public static function cards(): array
    {
        return [
            new ReferrersValue(),
            new ReferrerSystemBalance(),
            new ReferralsCountTrend(),
            new ReferralConfirmedPurchasedCountTrend(),
            new ReferralConfirmedPurchasedAmountTrend(),
            new ReferralPurchasedCountTrend(),
            new ReferralPurchasedAmountTrend(),
            new ReferrerTrend(),
            new ReferrersEarningTrend(),
        ];
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
