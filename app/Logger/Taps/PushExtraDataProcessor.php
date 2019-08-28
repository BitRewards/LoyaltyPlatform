<?php

namespace App\Logger\Taps;

use App\Logger\Processors\ExtraDataProcessor;
use Illuminate\Log\Logger;

class PushExtraDataProcessor
{
    public function __invoke(Logger $logger)
    {
        /** @var \Monolog\Logger $handler */
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new ExtraDataProcessor());
        }
    }
}
