<?php

namespace App\Services\Fiat\Tickers\Currency;

use App\DTO\TickerData;
use App\Services\Fiat\Tickers\AbstractTicker;

class RatesApiService extends AbstractTicker
{
    /**
     * @var string
     */
    public $baseCurrency = 'USD';

    /**
     * @var string
     */
    public $baseUrl = 'https://api.ratesapi.io/api/latest';

    public function supportedCurrencies(): array
    {
        return [
            'GBP',
            'HKD',
            'IDR',
            'ILS',
            'DKK',
            'INR',
            'CHF',
            'MXN',
            'CZK',
            'SGD',
            'THB',
            'HRK',
            'MYR',
            'NOK',
            'CNY',
            'BGN',
            'PHP',
            'SEK',
            'PLN',
            'ZAR',
            'CAD',
            'ISK',
            'BRL',
            'RON',
            'NZD',
            'TRY',
            'JPY',
            'RUB',
            'KRW',
            'USD',
            'HUF',
            'AUD',
        ];
    }

    public function isSupported(string $currency): bool
    {
        return in_array($currency, $this->supportedCurrencies());
    }

    public function requestTickerData($currency)
    {
        return $this->queryApi("?base={$this->baseCurrency}&symbols={$currency}");
    }

    public function convertResponse($response, $currency): TickerData
    {
        $response = \json_decode($response, true);

        return TickerData::make([
            'src' => $response['base'] ?? null,
            'price' => $response['rates'][$currency] ?? null,
            'dst' => $currency,
        ]);
    }
}
