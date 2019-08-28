<?php

namespace App\Services\Fiat\Tickers\Coin;

use App\DTO\TickerData;
use App\Services\Fiat\Tickers\AbstractTicker;

class CoinmarketcapService extends AbstractTicker
{
    const DEST_CURRENCY = 'USD';
    const TICKER_ETHEREUM = 1027;
    const TICKER_BITCOIN = 1;

    public static $TICKERS = [
        'ETH' => self::TICKER_ETHEREUM,
        'BTC' => self::TICKER_BITCOIN,
    ];

    public $baseUrl = 'https://api.coinmarketcap.com/';

    public function isSupported($coin): bool
    {
        return true;
    }

    public function getTickerId($coin)
    {
        return self::$TICKERS[$coin] ?? null;
    }

    public function requestTickerData($coin)
    {
        return $this->queryApi('v2/ticker/'.$this->getTickerId($coin).'/?convert='.self::DEST_CURRENCY);
    }

    public function convertResponse($response, $coin): TickerData
    {
        $response = \json_decode($response, true);

        return TickerData::make([
            'src' => $response['data']['symbol'],
            'price' => $response['data']['quotes'][self::DEST_CURRENCY]['price'],
            'dst' => self::DEST_CURRENCY,
        ]);
    }
}
