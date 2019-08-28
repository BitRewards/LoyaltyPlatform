<?php

namespace App\DTO\Factory;

use App\DTO\ExchangeRateData;
use App\Services\Fiat\FiatService;

class ExchangeRateFactory
{
    /**
     * @var FiatService
     */
    protected $fiatService;

    public function __construct(FiatService $fiatService)
    {
        $this->fiatService = $fiatService;
    }

    public function factoryFiatRates(string $currency): ExchangeRateData
    {
        $exchangeRate = new ExchangeRateData();
        $exchangeRate->currency = $currency;

        foreach ($this->fiatService::getCurrencyList() as $toCurrency) {
            $exchangeRate->exchangeRate[$toCurrency] = $this->fiatService->getExchangeRate($currency, $toCurrency);
        }

        return $exchangeRate;
    }
}
