<?php

namespace App\Nova\Traits;

use App\Models\Partner;
use Carbon\Carbon;
use Laravel\Nova\Metrics\TrendResult;
use Laravel\Nova\Metrics\ValueResult;

/**
 * @method ValueResult|TrendResult result($value = null)
 * @method ValueResult|TrendResult prefix($prefix = null)
 */
trait MetricTrait
{
    public function getFromDateTime(?int $defaultRange = null): ?Carbon
    {
        $range = (int) request()->get('range', $defaultRange);

        if (!$range) {
            return null;
        }

        return $this->getToDateTime()->subDays($range)->modify('00:00:00');
    }

    public function getToDateTime(): Carbon
    {
        return Carbon::now();
    }

    protected function getPartner(): ?Partner
    {
        return request()->user()->partner ?? null;
    }

    protected function getCurrency(): ?string
    {
        if ($this->getPartner()) {
            return \HAmount::sign($this->getPartner()->currency, true);
        }

        return null;
    }

    protected function getCurrencyPrefix(): ?string
    {
        $currency = $this->getCurrency();

        return $currency ? $currency.' ' : null;
    }

    /**
     * @param mixed $value
     *
     * @return TrendResult|ValueResult
     */
    protected function resultWithCurrency($value = null)
    {
        return $this
            ->result($value)
            ->prefix($this->getCurrency().' ');
    }
}
