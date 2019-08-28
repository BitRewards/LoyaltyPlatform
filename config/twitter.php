<?php

return array_replace_recursive([
    'consumer_key' => '**REMOVED**',
    'consumer_secret' => '**REMOVED**',
    'callback' => '/oauth',
], require __DIR__.'/stages/'.env('APP_ENV').'/twitter.php');
