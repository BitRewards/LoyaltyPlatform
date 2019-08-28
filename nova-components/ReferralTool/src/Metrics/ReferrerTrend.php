<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\User;
use App\Nova\Traits\TrendQueryTrait;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferrerTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;

    public function name(): string
    {
        return __('New referrers per day');
    }

    public function calculate(Request $request): TrendResult
    {
        $query = User::query()->where('partner_id', $request->user()->partner->id);
        $trendResult = $this->countByDays($request, $query, 'created_at');

        return $this
            ->resultWithSum($trendResult)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrer-trend';
    }
}
