<?php

namespace App\Http;

use App\Http\Middleware\AdministratorAuthentication;
use App\Http\Middleware\AuthClientApi;
use App\Http\Middleware\DisableCookies;
use App\Http\Middleware\PersonAuthentication;
use App\Http\Middleware\UsePartnerClient;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // 'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'administrator-authentication' => AdministratorAuthentication::class,
        'auth-person' => PersonAuthentication::class,
        'auth-client-api' => AuthClientApi::class,
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'role' => \App\Http\Middleware\AdminRoleCheck::class,
        'only-partners' => \App\Http\Middleware\OnlyPartners::class,
        'only-authenticated-clients' => \App\Http\Middleware\OnlyAuthenticatedClients::class,
        'only-confirmed-clients' => \App\Http\Middleware\OnlyConfirmedClients::class,
        'set-language-from-partner' => \App\Http\Middleware\SetLanguageFromPartner::class,
        'autologin-token' => \App\Http\Middleware\AutologinToken::class,
        'disable-cookies' => DisableCookies::class,
        'save-referrer' => \App\Http\Middleware\SaveReferrerKey::class,
        'treasury-client' => \App\Http\Middleware\TreasuryClient::class,
        'ethereum-to-lower' => \App\Http\Middleware\EthereumToLower::class,
        'use-partner-client' => UsePartnerClient::class,
        'cors' => \Barryvdh\Cors\HandleCors::class,
    ];
}
