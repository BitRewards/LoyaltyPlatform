<?php

namespace Bitrewards\DifferentiatedReferralCashback;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('differentiated-referral-cashback', __DIR__.'/../dist/js/field.js');
            Nova::style('differentiated-referral-cashback', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
