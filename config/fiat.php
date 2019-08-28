<?php

return array_replace_recursive([
    'apilayer' => [
        'access_key' => '**REMOVED**',
    ],
], require __DIR__.'/stages/'.env('APP_ENV').'/fiat.php');
