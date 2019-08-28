<?php

namespace App\Db;

use Illuminate\Support\ServiceProvider;

class CrmMigrationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new CrmMigrationCreator($app['files']);
        });
    }

    public function provides()
    {
        return ['migration.creator'];
    }
}
