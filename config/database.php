<?php

return array_replace_recursive([
    'fetch' => PDO::FETCH_OBJ,

    'default' => 'pgsql',

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'db',
            'port' => '**REMOVED**',
            'database' => '**REMOVED**',
            'username' => '**REMOVED**',
            'password' => '**REMOVED**',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],

    'migrations' => 'migrations',

    'redis' => [
        'cluster' => false,

        'default' => [
            'host' => '**REMOVED**',
            'password' => '**REMOVED**',
            'port' => '**REMOVED**',
            'database' => '**REMOVED**',
        ],

        'redis_lock' => [
            'ttl' => '**REMOVED**',
        ],
    ],
], require __DIR__.'/stages/'.env('APP_ENV').'/database.php');
