<?php

return array_replace_recursive([
    'driver' => 'smtp',
    'host' => env('MAIL_HOST', '**REMOVED**'),
    'port' => env('MAIL_PORT', '**REMOVED**'),
    'from' => ['address' => '**REMOVED**', 'name' => '**REMOVED**'],
    'encryption' => 'tls',
    'username' => env('MAIL_USERNAME', '**REMOVED**'),
    'password' => env('MAIL_PASSWORD', '**REMOVED**'),
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
    'send_to_whitelist' => null, // no whitelist by default. See testing/mail.php for the actual whitelist
], require __DIR__.'/stages/'.env('APP_ENV').'/mail.php');
