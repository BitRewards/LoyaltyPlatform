<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Validator::extend('ethaddress', 'App\\Rules\\EthereumAddressRule@validate', __('Enter correct Ethereum-address'));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
