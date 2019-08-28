<?php

namespace App\Providers;

use App\Administrator;
use App\Http\Controllers\Dashboard\LoginController;
use App\Nova\Metrics\Tools\AverageChequeIncreaseValue;
use App\Nova\Metrics\Tools\AveragePurchaseAmountTrend;
use App\Nova\Metrics\Tools\ContactsTrend;
use App\Nova\Metrics\Tools\IssuedPromoCodesCountValue;
use App\Nova\Metrics\Tools\PartnerPurchasesAmountTrend;
use App\Nova\Metrics\Tools\PurchaseCountTrend;
use App\Nova\Metrics\Tools\SentEmailsCountTrend;
use App\Nova\Metrics\Tools\ToolsCountValue;
use App\Nova\Metrics\Tools\UniqueUsersCountValue;
use App\Nova\Metrics\Tools\UsedPromoCodesCountValue;
use Bitrewards\ReferralTool\Cards\SimpleTable;
use Bitrewards\ReferralTool\Http\Middleware\Authorize;
use Bitrewards\ReferralTool\Metrics\SocialPostsTrend;
use Bitrewards\ReferralTool\ReferralTool;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Controllers\LoginController as NovaLoginController;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use Route;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();

        Route::middleware(['nova', Authorize::class])
            ->prefix('dashboard')
            ->group(dirname(__DIR__).'/../routes/nova.php');
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user instanceof Administrator && $user->partner_id;
        });
    }

    protected function authorization(): void
    {
        $this->gate();

        Nova::auth(function ($request) {
            return Gate::check('viewNova', [$request->user()]);
        });
    }

    protected function cards(): array
    {
        return [
            app(ToolsCountValue::class),
            app(PartnerPurchasesAmountTrend::class),
            app(AverageChequeIncreaseValue::class)
                ->withMeta([
                    'addClass' => 'increased-value',
                ]),
            app(AveragePurchaseAmountTrend::class),
            app(PurchaseCountTrend::class),
            app(IssuedPromoCodesCountValue::class),
            app(UsedPromoCodesCountValue::class),
            app(UniqueUsersCountValue::class),
            app(SentEmailsCountTrend::class),
            app(ContactsTrend::class),
            app(SocialPostsTrend::class),
            SimpleTable::make(__('Results for each tool'))
                ->setHeaders([
                    __('BitRewards tool'),
                    __('Amount of orders'),
                    __('Average order value'),
                    __('Number of orders'),
                ])
                ->setDataUrl('dashboard/table/tools-statistic'),
        ];
    }

    public function tools(): array
    {
        return [
            new ReferralTool(),
        ];
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(NovaLoginController::class, LoginController::class);
    }

    public static function extraTranslates(): array
    {
        return [
            'Tool statistic' => __('Tool statistic'),
            'Referral Tool' => __('Referral Tool'),
        ];
    }

    public static function jsonVariables(Request $request): array
    {
        $variables = Nova::jsonVariables(request());
        $variables = array_merge_recursive($variables, [
            'translations' => self::extraTranslates(),
        ]);

        return $variables;
    }
}
