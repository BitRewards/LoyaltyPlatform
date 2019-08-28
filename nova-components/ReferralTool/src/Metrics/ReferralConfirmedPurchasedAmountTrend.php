<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\Partner;
use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\PartnerStatisticService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferralConfirmedPurchasedAmountTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    public function name(): string
    {
        return __('Amount of confirmed referral purchases');
    }

    public function calculate(Request $request): TrendResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $query = app(PartnerStatisticService::class)->confirmedReferralStoreEntitiesQuery($partner);

        $trendResult = $this->sumByDay($request, $query, 'se.created_at', "(se.data->>'amountTotal')::float");

        return $this
            ->resultWithSum($trendResult)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referral-confirmed-purchased-amount';
    }
}
