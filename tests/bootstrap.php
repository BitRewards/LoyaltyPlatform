<?php

putenv('APP_ENV=codeception');

$dir = dirname(__DIR__);
$env = getenv('env') ?: 'codeception';

exec(
    implode('&&', [
        "cd $dir",
        "php artisan cache:clear --env=$env",
        "php artisan route:clear --env=$env",
        "php artisan config:clear --env=$env",
        "php artisan migrate:fresh --env=$env --seed",
    ]),
    $output,
    $return
);

if ($return) {
    echo implode("\n", $output);

    throw new RuntimeException('Migration failed');
}
