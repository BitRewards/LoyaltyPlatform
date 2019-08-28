<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\Partner;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\PartnerStatisticService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferralPurchasedCountTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;

    public function name(): string
    {
        return __('Referral orders, including unconfirmed');
    }

    public function calculate(Request $request): TrendResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $query = app(PartnerStatisticService::class)->referralStoreEntitiesQuery($partner);
        $trendResult = $this->countByDay($request, $query, 'se.created_at');

        return $this
            ->resultWithSum($trendResult)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referral-purchased-count';
    }
}
