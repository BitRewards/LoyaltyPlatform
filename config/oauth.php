<?php

return array_replace_recursive([
    'redirect_uri' => '/oauth',
    'display' => 'popup',
    'response_type' => 'code',
], require __DIR__.'/stages/'.env('APP_ENV').'/oauth.php');
