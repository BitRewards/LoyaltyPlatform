<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\ReferralStatisticService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferrersEarningTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    public function name(): string
    {
        return __('Amount of referrers earnings per day');
    }

    public function calculate(Request $request): TrendResult
    {
        $from = Carbon::now()->subDay($request->query->getInt('range'));
        $data = app(ReferralStatisticService::class)
            ->referralEarningTrendData($request->user()->partner, $from);

        $trendResult = $this->result()->trend($data);

        return $this
            ->resultWithSum($trendResult)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrers-earning';
    }
}
