<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\Partner;
use App\Nova\Traits\MetricTrait;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class ReferrerSystemBalance extends Value
{
    use MetricTrait;

    public function name(): string
    {
        return __('Referral system balance');
    }

    public function calculate(MetricRequest $request): ValueResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;

        return $this
            ->result($partner->balance)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrer-system-balance';
    }
}
