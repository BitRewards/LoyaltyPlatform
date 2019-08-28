<?php

namespace App\Services\Fiat\Tickers\Currency;

use App\DTO\TickerData;
use App\Services\Fiat\Tickers\AbstractTicker;

class ApiLayerService extends AbstractTicker
{
    const DEST_CURRENCY = 'USD';
    const TICKER_ETHEREUM = 1027;

    public static $TICKERS = [
        'ETH' => self::TICKER_ETHEREUM,
    ];

    public $baseUrl = 'http://www.apilayer.net/api/live';
    public $access_key;

    public function __construct()
    {
        $this->access_key = config('fiat.apilayer.access_key');
    }

    public function isSupported($coin): bool
    {
        return true;
    }

    public function getTickerId($coin)
    {
        return self::$TICKERS[$coin] ?? null;
    }

    public function requestTickerData($currency)
    {
        return $this->queryApi("?access_key={$this->access_key}&format=1&currencies={$currency}");
    }

    public function convertResponse($response, $currency): TickerData
    {
        $response = \json_decode($response, true);

        return TickerData::make([
            'src' => $response['source'] ?? null,
            'price' => $response['quotes'][self::DEST_CURRENCY.$currency] ?? null,
            'dst' => $currency,
        ]);
    }
}
