<?php

namespace Bitrewards\ReferralTool\Metrics;

use App\Models\User;
use App\Nova\Traits\MetricTrait;
use App\Services\TransactionService;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class ReferrerWithdrawsValue extends Value
{
    use DefaultRangesTrait;
    use MetricTrait;

    /**
     * @var bool
     */
    public $onlyOnDetail = true;

    public function name(): string
    {
        return __('Total amount of withdraws');
    }

    public function calculate(MetricRequest $request): ValueResult
    {
        /** @var User $referral */
        $referral = $request->findResourceOrFail()->resource;

        $from = Carbon::now()->subDay($request->get('range'));
        $amount = app(TransactionService::class)->getFiatWithdrawsAmount($referral, $from);

        return $this
            ->result($amount)
            ->prefix($this->getCurrencyPrefix())
            ->format('0,0');
    }

    public function uriKey(): string
    {
        return 'referrer-withdraws';
    }
}
