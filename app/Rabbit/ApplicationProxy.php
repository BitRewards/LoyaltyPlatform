<?php

namespace App\Rabbit;

use GL\Rabbit\ApplicationProxyInterface;

class ApplicationProxy implements ApplicationProxyInterface
{
    public function rollbackCurrentTransactions(): void
    {
        while (\DB::transactionLevel()) {
            \DB::rollBack();
        }
    }
}
