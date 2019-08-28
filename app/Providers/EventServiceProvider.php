<?php

namespace App\Providers;

use App\Events\Bonuses\CustomBonusGiven;
use App\Listeners\Auth\LogAuthenticated;
use App\Listeners\Bonuses\ReportCustomBonusEventToPartner;
use App\Listeners\LogEmail;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [
            LogEmail::class,
        ],

        Authenticated::class => [
            LogAuthenticated::class,
        ],

        CustomBonusGiven::class => [
            ReportCustomBonusEventToPartner::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
