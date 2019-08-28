<?php

namespace App\Logger\Handlers;

use Monolog\Handler\AbstractProcessingHandler;
use App\Models\Log;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected function write(array $record): void
    {
        $log = new Log();
        $log->level_name = $record['level_name'];
        $log->message = $record['message'];

        try {
            $log->save();
        } catch (\Exception $e) {
        }
    }
}
