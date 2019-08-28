<?php

return array_replace_recursive([
    'api_base_url' => env('LOCAL_TREASURY_API', '**REMOVED**'),
    'viewer' => '**REMOVED**',
    'allowed_ips' => ['**REMOVED**'],
    'exchange_address' => '',
    'exchange_api_key' => '',
    'exchange_withdraw_key' => '',
], require __DIR__.'/stages/'.env('APP_ENV').'/treasury.php');
