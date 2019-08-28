<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Collection;

class RewardService
{
    /**
     * @param Partner   $partner
     * @param User|null $user
     * @param array     $tags
     *
     * @return Collection|Reward[]
     */
    public function getPartnerRewardsForUser(Partner $partner, User $user = null, array $tags = []): Collection
    {
        $query = Reward::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'status' => Reward::STATUS_ENABLED,
            ])
            ->with('specialOfferReward');

        if (!empty($tags)) {
            $query->whereIn('tag', $tags);
        }

        return $query
            ->get()
            ->sort(
                function (Reward $a, Reward $b) use ($user) {
                    if (Reward::TYPE_BITREWARDS_PAYOUT == $a->type) {
                        return 999999999;
                    }

                    if (Reward::TYPE_BITREWARDS_PAYOUT == $b->type) {
                        return -999999999;
                    }

                    if (!$user) {
                        return $a->id - $b->id;
                    }

                    return -(($user->balance - $a->price) - ($user->balance - $b->price));
                }
            );
    }

    public function getWithdrawReward(Partner $partner): ?Reward
    {
        return Reward::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'type' => Reward::TYPE_BITREWARDS_PAYOUT,
            ])
            ->orderBy('id', 'asc')
            ->limit(1)
            ->first();
    }

    public function getFiatWithdrawReward(Partner $partner): ?Reward
    {
        return Reward::wherePartnerId($partner->id)
            ->whereType(Reward::TYPE_FIAT_WITHDRAW)
            ->first();
    }

    public function createFiatReferralReward(Partner $partner, array $data = []): Reward
    {
        $mainData = [
            'type' => Reward::TYPE_FIAT_WITHDRAW,
            'partner_id' => $partner->id,
        ];

        $defaults = [
            'status' => Reward::STATUS_DISABLED,
            'title' => __('Fiat withdrawal'),
        ];

        $data = $mainData + $data + $defaults;

        $reward = new Reward($data);
        $reward->saveOrFail();

        return $reward;
    }
}
