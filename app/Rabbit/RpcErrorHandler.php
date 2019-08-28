<?php

namespace App\Rabbit;

use GL\Rabbit\RpcErrorHandlerInterface;
use GL\Rabbit\Exception\RpcException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RpcErrorHandler implements RpcErrorHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(LoggerInterface $logger = null)
    {
        $logger = $logger ?? new NullLogger();

        $this->setLogger($logger);
    }

    /**
     * @param \Throwable $exception
     *
     * @return RpcException
     */
    public function transform(\Throwable $exception): RpcException
    {
        return new RpcException(
            $exception->getMessage()
        );
    }

    public function log(\Throwable $exception): void
    {
        $this->logger->error($exception->getMessage(), ['trace' => $exception->getTrace()]);
    }
}
