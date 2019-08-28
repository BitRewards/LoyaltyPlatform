<?php

/**
 * Laravel - A PHP Framework For Web Artisans.
 *
 * @author   Taylor Otwell <taylor@laravel.com>
 */

// $uri = $_SERVER['REQUEST_URI'];
// $method = $_SERVER['REQUEST_METHOD'];
// file_put_contents('/crm/shared/storage/logs' . '/debug.log', date("d.m.Y H:i:s") . " - $method $uri - \$_REQUEST: \n" . json_encode($_REQUEST, JSON_UNESCAPED_UNICODE) . "\n\n\n", FILE_APPEND | LOCK_EX);

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

/**
 * @var Illuminate\Foundation\Application
 */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
