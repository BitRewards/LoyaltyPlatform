<?php

use Bitrewards\DifferentiatedReferralCashback\FieldServiceProvider;

return array_replace_recursive([
    'name' => 'BitRewards',

    'env' => env('APP_ENV'),

    'debug' => false,

    'url' => '**REMOVED**',

    'timezone' => 'UTC',

    'locale' => 'en',

    'fallback_locale' => 'ru',

    'key' => '**REMOVED**',

    'cipher' => '**REMOVED**',

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        App\Providers\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        Laravel\Tinker\TinkerServiceProvider::class,
        Spatie\Fractal\FractalServiceProvider::class,
        ArgentCrusade\Flysystem\Selectel\SelectelServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        LaravelPropertyBag\ServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\NovaServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        App\Db\CrmMigrationServiceProvider::class,

        App\Crud\CrudServiceProvider::class,
        Backpack\Generators\GeneratorsServiceProvider::class,

        Fedeisas\LaravelMailCssInliner\LaravelMailCssInlinerServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Aws\Laravel\AwsServiceProvider::class,

        \App\Providers\ValidatorServiceProvider::class,
        \App\Providers\ApiServiceProvider::class,
        \Collective\Html\HtmlServiceProvider::class,

        FieldServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'LaraRedis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        // 'HTML' => HtmlFacade::class
        'Sentry' => Sentry\SentryLaravel\SentryFacade::class,
        'Registry' => \App\Db\Factory\Facades\Registry::class,
    ],
], require __DIR__.'/stages/'.env('APP_ENV').'/app.php');
