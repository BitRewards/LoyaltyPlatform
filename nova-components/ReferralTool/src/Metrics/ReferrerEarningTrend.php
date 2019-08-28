<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\ReferralStatisticService;
use App\User;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferrerEarningTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    public $onlyOnDetail = true;

    public function name(): string
    {
        return __('Amount of earnings per day');
    }

    public function calculate(MetricRequest $request): TrendResult
    {
        /** @var User $referrer */
        $referrer = $request->findResourceOrFail()->resource;
        $from = Carbon::now()->subDay($request->query->getInt('range'));
        $data = app(ReferralStatisticService::class)
            ->referralEarningTrendData($referrer->partner, $from, null, $referrer);

        $trendData = $this->result()->trend($data);

        return $this
            ->resultWithSum($trendData)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrer-earning';
    }
}
