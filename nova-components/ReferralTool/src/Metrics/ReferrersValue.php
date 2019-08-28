<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\Partner;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class ReferrersValue extends Value
{
    public function name(): string
    {
        return __('Total referrers');
    }

    public function calculate(MetricRequest $request): ValueResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $referrersCount = $partner->users()->count();

        return $this
            ->result($referrersCount)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'total-referrers';
    }
}
