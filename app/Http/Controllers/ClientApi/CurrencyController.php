<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\ExchangeRateFactory;
use App\Http\Controllers\ClientApiController;
use App\Services\Fiat\FiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends ClientApiController
{
    /**
     * @var ExchangeRateFactory
     */
    private $exchangeRateFactory;

    public function __construct(ExchangeRateFactory $exchangeRateFactory)
    {
        $this->exchangeRateFactory = $exchangeRateFactory;
    }

    public function fiatRates(Request $request): JsonResponse
    {
        $exchangeRates = $this
            ->exchangeRateFactory
            ->factoryFiatRates($request->currency ?? FiatService::COIN_BIT);

        return $this->responseJson($exchangeRates);
    }
}
