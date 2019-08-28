<?php

namespace App\Models;

use App\Services\ActionService;
use App\Services\ReferralStatisticService;
use App\Services\RewardService;

class FiatWithdrawService
{
    /**
     * @var ReferralStatisticService
     */
    protected $referralStatisticService;

    /**
     * @var ActionService
     */
    protected $actionService;

    /**
     * @var RewardService
     */
    protected $rewardService;

    public function __construct(
        ReferralStatisticService $referralStatisticService,
        ActionService $actionService,
        RewardService $rewardService
    ) {
        $this->referralStatisticService = $referralStatisticService;
        $this->actionService = $actionService;
        $this->rewardService = $rewardService;
    }

    public function getWithdrawsAmount(User $user): float
    {
        $reward = $this->rewardService->getFiatWithdrawReward($user->partner);

        if (!$reward) {
            return 0.;
        }

        return $user
            ->transactions()
            ->where('reward_id', $reward->id)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->sum('balance_change');
    }

    public function getBlockedAmount(User $user): float
    {
        $reward = $this
            ->rewardService
            ->getFiatWithdrawReward($user->partner);

        if (!$reward) {
            return 0.;
        }

        return (float) $user
            ->transactions()
            ->where('reward_id', $reward->id)
            ->where('status', Transaction::STATUS_PENDING)
            ->sum('balance_change');
    }

    public function availableForWithdraw(User $user): float
    {
        return $this
                ->referralStatisticService
                ->getCashBackAmount($user->partner, null, null, $user)
            - $this->getBlockedAmount($user)
            - $this->getWithdrawsAmount($user);
    }
}
