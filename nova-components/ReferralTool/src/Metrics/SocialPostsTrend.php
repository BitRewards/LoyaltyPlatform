<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class SocialPostsTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    public function name(): string
    {
        return __('Social posts per day');
    }

    public function calculate(Request $request): TrendResult
    {
        $apiClient = (new ApiClient())->make($this->getPartner());
        $trendData = $apiClient->getWidgetPostCountTrend($this->getFromDateTime($this->getDefaultRange()));
        $result = $this->result()->trend($trendData);

        return $this->resultWithSum($result);
    }

    public function authorize(Request $request): bool
    {
        $apiClient = (new ApiClient())->make($this->getPartner());
        $trendData = $apiClient->getWidgetPostCountTrend($this->getFromDateTime($this->getDefaultRange()));

        if (!$trendData) {
            return false;
        }

        return parent::authorize($request);
    }

    public function uriKey(): string
    {
        return 'social-posts-trend';
    }
}
