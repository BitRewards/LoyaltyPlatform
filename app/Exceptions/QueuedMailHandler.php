<?php

namespace App\Exceptions;

use Monolog\Handler\AbstractProcessingHandler;

class QueuedMailHandler extends AbstractProcessingHandler
{
    private $adminEmails = ['**REMOVED**'];

    protected function write(array $record)
    {
        if (!\App::isLocal()) {
            foreach ($this->adminEmails as $email) {
                \Mail::to($email)->queue(new \App\Mail\Logger($record));
            }
        }
    }
}
