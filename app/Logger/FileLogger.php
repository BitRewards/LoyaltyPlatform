<?php

namespace App\Logger;

use App\Logger\Formatters\BetterFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FileLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('custom_file');

        $fileHandler = new RotatingFileHandler(storage_path('/logs/crm_{date}.log'), 0, Logger::DEBUG, true, null, true);
        $fileHandler->setFilenameFormat('crm_{date}', 'Ymd');
        $fileHandler->setFormatter(new BetterFormatter('d.m.y H:i:s'));
        $logger->pushHandler($fileHandler);

        return $logger;
    }
}
