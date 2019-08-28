<?php

return [
    'trace' => [
        'excludeClasses' => [
            'HandleExceptions',
            'Pipeline',
            'Router',
            'Route',
            'Kernel',
        ],

        'excludeFiles' => [
            'Pipeline.php',
        ],

        'maxArrayKeys' => 1000,
        'maxJsonLength' => 100000,
        'maxStringLength' => 10000,
    ],
];
