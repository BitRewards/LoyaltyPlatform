<?php

namespace App\Providers;

use App\Models\HelpItem;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\Action;
use App\Models\SavedCoupon;
use App\Models\SpecialOfferAction;
use App\Models\SpecialOfferReward;
use App\Models\Transaction;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    const API_CLIENT_PREFIX = 'api-client';
    const API_DASHBOARD = 'dashboard';

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        parent::boot();

        Route::model('user', \App\User::class);
        Route::model('cashierUser', \App\User::class);
        Route::model('helpItem', HelpItem::class);
        Route::model('transaction', \App\Models\Transaction::class);
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        if (!\App::isLocal()) {
            // Cloudflare
            \Request::setTrustedProxies(config('cloudflare.ipsv4'), Request::HEADER_X_FORWARDED_ALL);
        }

        \URL::forceScheme('https');

        $this->mapWebRoutes();

        $this->mapApiRoutes();

        $this->mapTreasuryRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes()
    {
        \Route::group([
            'middleware' => ['web', 'set-language-from-partner'],
            'namespace' => $this->namespace,
        ], function (Router $router) {
            require base_path('routes/admin.php');
            require base_path('routes/other.php');
        });

        \Route::group([
            'prefix' => 'app/{partner}/',
            'namespace' => $this->namespace,
            'middleware' => ['web', 'set-language-from-partner'],
        ],
            function (Router $router) {
                $router->bind('partner', function ($partnerKey) {
                    return Partner::where('key', $partnerKey)->first();
                });

                $router->bind('reward', function ($id) {
                    return Reward::where('id', $id)->first();
                });

                $router->bind('transaction', function ($id) {
                    return Transaction::where('id', $id)->first();
                });

                $router->bind('action', function ($id) {
                    return Action::where('id', $id)->first();
                });

                $router->bind('code', function ($id) {
                    return Code::find($id);
                });

                $router->bind('specialOfferAction', function ($id) {
                    return SpecialOfferAction::where('id', $id)->first();
                });

                $router->bind('specialOfferReward', function ($id) {
                    return SpecialOfferReward::where('id', $id)->first();
                });

                $router->bind('savedCoupon', function ($id) {
                    return SavedCoupon::where('id', $id)->first();
                });

                require base_path('routes/client.php');
            }
        );
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes()
    {
        \Route::group([
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api.php');
            require base_path('routes/api-legacy.php');
            require base_path('routes/api-cashier.php');
        });

        \Route::group([
            // используем middleware auth-client-api для аутентификации по HTTP-заголовку
            // для того чтобы веб работал, надо будет все-таки модицифировать наш фронтенд (к Славе),
            // чтобы с любым аяксом посылался хедер X-Auth-Token - иначе будут разные сайд эффекты.
            // auth-client-api = Http/Middleware/AuthClientApi.php
            'middleware' => ['api', 'auth-client-api', 'set-language-from-partner'],
            'namespace' => $this->namespace,
            'prefix' => self::API_CLIENT_PREFIX,
        ], function ($router) {
            require base_path('routes/api-client.php');
        });
    }

    public static function isRESTApiRequest(Request $request): bool
    {
        $parts = explode('/', $request->path());

        return in_array(reset($parts), [
            self::API_CLIENT_PREFIX,
//            self::API_DASHBOARD,
        ]);
    }

    /**
     * Define the "treasury" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapTreasuryRoutes()
    {
        \Route::group([
            'middleware' => ['api', 'treasury-client', 'ethereum-to-lower'],
            'namespace' => $this->namespace,
            'prefix' => 'api/treasury',
        ], function ($router) {
            require base_path('routes/treasury.php');
        });
    }
}
