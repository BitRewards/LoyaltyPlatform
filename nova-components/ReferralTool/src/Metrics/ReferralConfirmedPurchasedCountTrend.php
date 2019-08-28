<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\Partner;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\PartnerStatisticService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferralConfirmedPurchasedCountTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;

    public function name(): string
    {
        return __('Confirmed referral purchases');
    }

    public function calculate(Request $request): TrendResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $query = app(PartnerStatisticService::class)->confirmedReferralStoreEntitiesQuery($partner);
        $trendResult = $this->countByDay($request, $query, 'se.created_at');

        return $this
            ->resultWithSum($trendResult)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referral-confirmed-purchased-count';
    }
}
