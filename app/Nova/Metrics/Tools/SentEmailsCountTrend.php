<?php

namespace App\Nova\Metrics\Tools;

use App\Models\SentEmail;
use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class SentEmailsCountTrend extends Trend
{
    use DefaultRangesTrait;
    use TrendQueryTrait;
    use MetricTrait;

    /**
     * @var ApiClient
     */
    protected $apiClient;

    public function __construct(ApiClient $apiClient, $component = null)
    {
        parent::__construct($component);

        $this->apiClient = $apiClient;
    }

    public function name(): string
    {
        return __('Number of sent emails');
    }

    public function calculate(Request $request): TrendResult
    {
        $query = SentEmail::query()
            ->join('users', 'users.email', 'sent_emails.email')
            ->where('users.partner_id', $this->getPartner()->id);

        $trendData = $this->countByDay($request, $query, 'sent_emails.created_at')->trend;
        $giftdData = $this
            ->apiClient
            ->make($this->getPartner())
            ->getSentEmailsTrend($this->getFromDateTime());

        foreach ($trendData as $date => $value) {
            $trendData[$date] += $giftdData[$date] ?? 0;
        }

        return $this->resultWithSum($this->result()->trend($trendData));
    }

    public function uriKey(): string
    {
        return 'tools-emails-count';
    }
}
