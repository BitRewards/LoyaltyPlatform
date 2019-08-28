<?php

namespace App\Logger\Handlers;

use Monolog\Handler\AbstractProcessingHandler;

/**
 * @deprecated use RotatingFileHandler instead
 */
class FileHandler extends AbstractProcessingHandler
{
    /**
     * @var string
     */
    protected $path;

    public function __construct($path)
    {
        parent::__construct();

        $this->path = $path;
    }

    protected function write(array $record)
    {
        file_put_contents($this->path, $record['formatted'], FILE_APPEND | LOCK_EX);
    }
}
