<?php

namespace App\Console\Commands\Test;

use App\Services\Fiat\FiatService;
use Illuminate\Console\Command;

class FiatRates extends Command
{
    protected $signature = 'test:fiat-rates {currencyFrom} {currencyTo}';

    public function handle()
    {
        echo app(FiatService::class)->getExchangeRate($this->argument('currencyFrom'), $this->argument('currencyTo'), true);
    }
}
