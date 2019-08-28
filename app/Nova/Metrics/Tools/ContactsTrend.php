<?php

namespace App\Nova\Metrics\Tools;

use App\Nova\Traits\MetricTrait;
use App\Nova\Traits\TrendQueryTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ContactsTrend extends Trend
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
        return __('Received contacts by BitRewards tools');
    }

    public function calculate(Request $request): TrendResult
    {
        $giftdContactsTrend = $this
            ->apiClient
            ->make($this->getPartner())
            ->getContactsTrend($this->getFromDateTime());

        $emailContacts = $this->getEmailList($giftdContactsTrend);
        $phoneContacts = $this->getPhoneNumberList($giftdContactsTrend);

        $query = \DB::query()
            ->from('users')
            ->join('persons', 'persons.id', 'users.person_id')
            ->join('credentials', 'credentials.person_id', 'persons.id')
            ->where('users.partner_id', $this->getPartner()->id)
            ->where('users.created_at', '>=', $this->getFromDateTime())
            ->whereNotIn('credentials.email', $emailContacts)
            ->whereNotIn('credentials.phone', $phoneContacts);

        $trendData = $this
            ->countByDay($request, $query, 'credentials.created_at', 'credentials.*')
            ->trend;

        foreach ($trendData as $date => $count) {
            $trendData[$date] += count($giftdContactsTrend[$date] ?? []);
        }

        return $this->resultWithSum($this->result()->trend($trendData));
    }

    protected function getEmailList(array $giftdContactsTrend): array
    {
        $giftdContactsTrend = array_map(static function ($item) {
            return 'email' === $item['type'];
        }, array_flatten($giftdContactsTrend, 1));

        return array_column($giftdContactsTrend, 'contact');
    }

    protected function getPhoneNumberList(array $giftdContactsTrend): array
    {
        $giftdContactsTrend = array_map(static function ($item) {
            return 'phone' === $item['type'];
        }, array_flatten($giftdContactsTrend, 1));

        return array_column($giftdContactsTrend, 'contact');
    }

    public function uriKey(): string
    {
        return 'tools-contacts-count';
    }
}
