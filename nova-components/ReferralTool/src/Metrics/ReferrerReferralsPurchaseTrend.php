<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\User;
use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\ReferralStatisticService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferrerReferralsPurchaseTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    public $onlyOnDetail = true;

    public function name(): string
    {
        return __('Number of referral purchases per day');
    }

    public function calculate(MetricRequest $request): TrendResult
    {
        /** @var User $referrer */
        $referrer = $request->findResourceOrFail()->resource;
        $data = app(ReferralStatisticService::class)
            ->referralPurchasesTrendData($referrer, (int) $request->query->getInt('range'));

        $trendResult = $this->result()->trend($data);

        return $this
            ->resultWithSum($trendResult)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrals-purchase';
    }
}
