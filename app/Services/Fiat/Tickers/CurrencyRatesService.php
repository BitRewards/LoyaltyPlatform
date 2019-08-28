<?php

namespace App\Services\Fiat\Tickers;

use App\DTO\TickerData;

class CurrencyRatesService
{
    protected $tickerServices = [];

    public function addTickerService(AbstractTicker $ticker, int $priority = 0): self
    {
        $this->tickerServices[] = [
            'ticker' => $ticker,
            'priority' => $priority,
        ];

        return $this;
    }

    /**
     * @return AbstractTicker[]
     */
    public function getTickerServices(): array
    {
        return collect($this->tickerServices)
            ->sortBy(function ($ticker) {
                return $ticker['priority'];
            }, SORT_NUMERIC, true)
            ->pluck('ticker')
            ->toArray();
    }

    public function getTicker(string $currency): TickerData
    {
        foreach ($this->getTickerServices() as $tickerService) {
            if ($tickerService->isSupported($currency)) {
                dump(get_class($tickerService));

                return $tickerService->getTicker($currency);
            }
        }

        throw new InvalidTickerResponse("Currency '{$currency}' not supported");
    }
}
