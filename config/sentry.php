<?php

return [
    'dsn' => 'production' === env('APP_ENV') ? env('SENTRY_LARAVEL_DSN', '**REMOVED**') : null,

    'breadcrumbs' => [
        'sql_bindings' => true,
    ],
];
