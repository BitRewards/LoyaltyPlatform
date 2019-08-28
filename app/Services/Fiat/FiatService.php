<?php

namespace App\Services\Fiat;

use App\Services\Fiat\Tickers\Coin\HitBtcService;
use App\Services\Fiat\Tickers\Coin\CoinmarketcapService;
use App\Services\Fiat\Tickers\CurrencyRatesService;
use App\Services\Fiat\Tickers\InvalidTickerResponse;
use Illuminate\Support\Facades\Cache;

class FiatService
{
    public const COIN_ETH = 'ETH';
    public const COIN_BIT = 'BIT';
    public const COIN_BTC = 'BTC';

    public static $COIN_LIST = [
        'ETH', 'BIT', 'BTC',
    ];

    const FIXED_BIT_TO_ETH_RATE = '0.00003472';
    const FIAT_EXTRA_PERCENT = '5';

    public static function getCurrencyList()
    {
        return array_map(function ($code) {
            return \HAmount::sISO4217($code);
        }, array_keys(\HAmount::getCurrencyList()));
    }

    public function getExchangeRate($currencyFrom, $currencyTo, $forceUpdate = false)
    {
        $key = 'fiat_rate_'.$currencyFrom.'_'.$currencyTo;

        if ($currencyFrom === $currencyTo) {
            return 1;
        }

        if (!$forceUpdate && $rate = Cache::store('redis')->get($key)) {
            return $rate;
        }

        try {
            $from_usd_rate = $this->getUSDRate($currencyFrom, $forceUpdate);
            $to_usd_rate = $this->getUSDRate($currencyTo, $forceUpdate);

            $value = 1 / $from_usd_rate * $to_usd_rate;

            $value *= (1 + self::FIAT_EXTRA_PERCENT / 100);
            Cache::store('redis')->put($key, $value, 24 * 3600);

            return $value;
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());

            return null;
        }
    }

    protected function isCoin($currency)
    {
        return in_array($currency, self::$COIN_LIST, true);
    }

    protected function isCurrency($currency)
    {
        return in_array($currency, self::getCurrencyList(), true);
    }

    protected function getCoinRate($coin)
    {
        try {
            $coinService = new CoinmarketcapService();

            if ('BIT' === $coin) {
                $eth_to_usd = $coinService->getTicker('ETH');
                $bit_to_eth = app(HitBtcService::class)->getExchangeRate('BIT', 'ETH');

                $bit_to_usd = $eth_to_usd->price * $bit_to_eth->price;

                return 1 / $bit_to_usd;
            } else {
                $coin_to_usd = $coinService->getTicker($coin);

                return 1 / $coin_to_usd->price;
            }
        } catch (InvalidTickerResponse $e) {
            \Log::error($e->getMessage());

            return null;
        }
    }

    protected function getFiatRate($currency)
    {
        if ('USD' === $currency) {
            return 1;
        }

        try {
            $usd_to_fiat = app(CurrencyRatesService::class)->getTicker($currency);

            return $usd_to_fiat->price;
        } catch (InvalidTickerResponse $e) {
            \Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * @param $currency
     *
     * @return float|int|null
     *
     * @throws \Exception
     */
    public function getUSDRate($currency, $forceUpdate)
    {
        $key = 'usd_rate_'.$currency;

        if (!$forceUpdate && $rate = Cache::store('redis')->get($key)) {
            return $rate;
        }

        if ($this->isCoin($currency)) {
            $value = $this->getCoinRate($currency);
        } elseif ($this->isCurrency($currency)) {
            $value = $this->getFiatRate($currency);
        } else {
            throw new \Exception('Undefined currency: '.$currency);
        }

        if (empty($value)) {
            throw new \Exception('Invalid rate response');
        }

        Cache::store('redis')->put($key, $value, 30 * 24 * 3600);

        return $value;
    }

    public function getBitToFiatRate($currency, $forceUpdate = false)
    {
        return $this->getExchangeRate('BIT', $currency, $forceUpdate);
    }

    public function getRate($currency, $forceUpdate = false)
    {
        $key = 'fiat_rate_'.$currency;

        if (!$forceUpdate && $rate = Cache::store('redis')->get($key)) {
            return $rate;
        }

        try {
            $eth_to_usd = app(CoinmarketcapService::class)->getTicker('ETH');
            $bit_to_eth = self::FIXED_BIT_TO_ETH_RATE;
            $usd_to_fiat = app(CurrencyRatesService::class)->getTicker($currency);

            $value = $eth_to_usd->price * $bit_to_eth * $usd_to_fiat->price;
            $value *= (1 + self::FIAT_EXTRA_PERCENT / 100);
            Cache::store('redis')->put($key, $value, 24 * 3600);

            return $value;
        } catch (InvalidTickerResponse $e) {
            \Log::error($e->getMessage());

            return null;
        }
    }

    public function exchangeBit($toCurrencyId, $amount, bool $forceUpdate = false): float
    {
        $currency = \HAmount::sISO4217($toCurrencyId);
        $exchangeRate = $this->getBitToFiatRate($currency, $forceUpdate);

        return \HAmount::floor($amount * $exchangeRate);
    }
}
