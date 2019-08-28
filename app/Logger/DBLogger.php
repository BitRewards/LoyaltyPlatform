<?php

namespace App\Logger;

use App\Logger\Handlers\DatabaseHandler;
use Monolog\Logger;

class DBLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('db');
        $logger->pushHandler(new DatabaseHandler());

        return $logger;
    }
}
