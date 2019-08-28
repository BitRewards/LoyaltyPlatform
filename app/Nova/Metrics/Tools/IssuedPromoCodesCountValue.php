<?php

namespace App\Nova\Metrics\Tools;

use App\Models\Partner;
use App\Nova\Traits\MetricTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class IssuedPromoCodesCountValue extends Value
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
        return __('Number of issued promo-codes for all BitRewards tools');
    }

    public function calculate(NovaRequest $request): ValueResult
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $apiClient = $this->apiClient->make($partner);
        $reportData = $apiClient->getReportData(
            $this->getFromDateTime($this->getDefaultRange()),
            $this->getToDateTime()
        );

        return $this
            ->result($reportData->numberOfIssuedPromoCodes)
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'tools-issued-promo-codes-count';
    }
}
