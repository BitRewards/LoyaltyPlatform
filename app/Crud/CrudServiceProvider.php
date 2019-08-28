<?php

namespace App\Crud;

class CrudServiceProvider extends \Backpack\CRUD\CrudServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $_SERVER['BACKPACK_CRUD_VERSION'] = $this::VERSION;

        // LOAD THE VIEWS

        $dir = realpath(__DIR__.'/../../vendor/backpack/crud/src');

        // - first the published/overwritten views (in case they have any changes)
        $this->loadViewsFrom(resource_path('views/vendor/backpack/crud'), 'crud');
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath($dir.'/resources/views'), 'crud');

        // PUBLISH FILES

        // publish lang files
        $this->publishes([$dir.'/resources/lang' => resource_path('lang/vendor/backpack')], 'lang');

        // publish views
        $this->publishes([$dir.'/resources/views' => resource_path('views/vendor/backpack/crud')], 'views');

        // publish config file
        $this->publishes([$dir.'/config' => config_path()], 'config');

        // publish public Backpack CRUD assets
        $this->publishes([$dir.'/public' => public_path('vendor/backpack')], 'public');

        // publish custom files for elFinder
        $this->publishes([
            $dir.'/config/elfinder.php' => config_path('elfinder.php'),
            $dir.'/resources/views-elfinder' => resource_path('views/vendor/elfinder'),
        ], 'elfinder');

        // AUTO PUBLISH
        if (\App::environment('local')) {
            if ($this->shouldAutoPublishPublic()) {
                \Artisan::call('vendor:publish', [
                    '--provider' => 'App\Crud\CrudServiceProvider',
                    '--tag' => 'public',
                ]);
            }
        }

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            $dir.'/config/backpack/crud.php',
            'backpack.crud'
        );
    }

    private function shouldAutoPublishPublic()
    {
        $crudPubPath = public_path('vendor/backpack/crud');

        if (!is_dir($crudPubPath)) {
            return true;
        }

        return false;
    }
}
