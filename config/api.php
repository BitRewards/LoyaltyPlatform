<?php

return array_replace_recursive([
    'host' => '**REMOVED**',
    'version' => '1.0',
    'scheme' => 'https',
    'base_path' => 'api',
], require __DIR__.'/stages/'.env('APP_ENV').'/api.php');
