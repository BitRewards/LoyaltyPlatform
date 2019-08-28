<?php

namespace Bitrewards\ReferralTool\Metrics;

use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use App\Services\ReferralStatisticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferralsCountTrend extends Trend
{
    use DefaultRangesTrait;

    public function name(): string
    {
        return __('Referral visitors for period');
    }

    public function calculate(Request $request): TrendResult
    {
        $from = Carbon::now()->subDay($request->query->getInt('range'));

        $dailyStatistic = app(ReferralStatisticService::class)
            ->getUniqueReferralsCountTrendData($request->user()->partner, $from);

        return $this
            ->result(array_sum($dailyStatistic))
            ->trend($dailyStatistic)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrals-count';
    }

    public function authorize(Request $request)
    {
        return $this->authorizedToSee($request) && \Auth::user()->partner;
    }
}
