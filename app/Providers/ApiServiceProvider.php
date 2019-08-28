<?php

namespace App\Providers;

use App\Services\Treasury\IApiClient;
use App\Services\Treasury\MockApiClient;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(IApiClient::class, MockApiClient::class);
    }
}
