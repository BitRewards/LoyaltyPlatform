<?php

return array_replace_recursive([
    'api_base_url' => '**REMOVED**',
    'test_partner' => [
        'id' => '**REMOVED**',
        'code' => '**REMOVED**',
        'user_id' => '**REMOVED**',
        'api_key' => '**REMOVED**',
    ],
    'admin' => [
        'user_id' => '**REMOVED**',
        'api_key' => '**REMOVED**',
    ],
], require __DIR__.'/stages/'.env('APP_ENV').'/giftd.php');
