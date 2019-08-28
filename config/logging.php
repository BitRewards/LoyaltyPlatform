<?php

use App\Logger\DBLogger;
use App\Logger\FileLogger;
use App\Logger\Taps\PushExtraDataProcessor;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['file', 'db'],
        ],

        'file' => [
            'driver' => 'custom',
            'via' => FileLogger::class,
            'tap' => [PushExtraDataProcessor::class],
        ],

        'db' => [
            'driver' => 'custom',
            'via' => DBLogger::class,
        ],
    ],
];
