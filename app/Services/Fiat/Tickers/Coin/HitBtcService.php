<?php

namespace App\Services\Fiat\Tickers\Coin;

use App\DTO\TickerData;
use App\Services\Fiat\Tickers\AbstractTicker;
use App\Services\Fiat\Tickers\InvalidTickerResponse;

class HitBtcService extends AbstractTicker
{
    public $baseUrl = 'https://api.hitbtc.com/';

    public function isSupported($coin): bool
    {
        return true;
    }

    public function requestTickerData($coin)
    {
        return $this->queryApi('/api/2/public/ticker/'.$coin);
    }

    /**
     * @param $currencyFrom
     * @param $currencyTo
     *
     * @return TickerData|null
     *
     * @throws InvalidTickerResponse
     */
    public function getExchangeRate($currencyFrom, $currencyTo)
    {
        $response = $this->requestTickerData($currencyFrom.$currencyTo);

        if (empty($response)) {
            throw new InvalidTickerResponse('Empty response from ticker');
        }

        if (!empty($response['error'])) {
            throw new InvalidTickerResponse('Empty response from ticker');
        }

        return $this->convertResponseExtended($response, $currencyFrom, $currencyTo);
    }

    /**
     * @param $response
     * @param $currencyFrom
     * @param $currencyTo
     *
     * @return TickerData
     *
     * @throws InvalidTickerResponse
     */
    public function convertResponseExtended($response, $currencyFrom, $currencyTo)
    {
        $data = $this->convertResponse($response, null);
        $data->src = $currencyFrom;
        $data->dst = $currencyTo;

        return $data;
    }

    /**
     * @param $response
     * @param $coin
     *
     * @return TickerData
     *
     * @throws InvalidTickerResponse
     */
    public function convertResponse($response, $coin): TickerData
    {
        $response = \json_decode($response, true);

        return TickerData::make([
            'src' => null,
            'price' => $response['last'],
            'dst' => null,
        ]);
    }
}
