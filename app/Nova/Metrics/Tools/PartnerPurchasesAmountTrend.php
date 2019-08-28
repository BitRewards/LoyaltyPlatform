<?php

namespace App\Nova\Metrics\Tools;

use App\Models\Partner;
use App\Nova\Traits\MetricTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class PartnerPurchasesAmountTrend extends Trend
{
    use DefaultRangesTrait;
    use MetricTrait;

    protected $apiClient;

    public function __construct(ApiClient $apiClient, $component = null)
    {
        parent::__construct($component);

        $this->apiClient = $apiClient;
    }

    public function name(): string
    {
        return __('Total amount of purchases for all BitRewards Tools');
    }

    public function calculate(Request $request): TrendResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $apiClient = $this->apiClient->make($partner);
        $reportData = $apiClient->getReportData(
            $this->getFromDateTime($this->getDefaultRange()),
            $this->getToDateTime()
        );

        return $this
            ->resultWithCurrency($reportData->totalAmountOfPurchases)
            ->format('0,0[.]00');
    }

    public function uriKey(): string
    {
        return 'tools-partner-purchases-amount';
    }
}
