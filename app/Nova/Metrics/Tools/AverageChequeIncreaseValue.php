<?php

namespace App\Nova\Metrics\Tools;

use App\Models\Partner;
use App\Nova\Traits\MetricTrait;
use App\Services\Giftd\ApiClient;
use App\Services\PartnerService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class AverageChequeIncreaseValue extends Value
{
    use DefaultRangesTrait;
    use MetricTrait;

    public $component = 'extended-value-metric';

    protected $apiClient;

    public function __construct(ApiClient $apiClient, $component = null)
    {
        parent::__construct($component);

        $this->apiClient = $apiClient;
    }

    public function name(): string
    {
        return __('Average order value increase');
    }

    public function calculate(Request $request): ValueResult
    {
        return $this
            ->result($this->averageChequeIncrease($request))
            ->suffix('%')
            ->format('0,0.0[0]');
    }

    protected function averageChequeIncrease(Request $request): float
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;

        return app(PartnerService::class)
            ->getAverageChequeIncrease($partner, $request->get('range', $this->getDefaultRange()));
    }

    public function authorize(Request $request): bool
    {
        if ($this->averageChequeIncrease($request) > 0) {
            return parent::authorize($request);
        }

        return false;
    }

    public function uriKey(): string
    {
        return 'tools-average-cheque-increase';
    }
}
