<?php

namespace App\Transformers;

use HAmount;
use HReward;
use App\Models\Reward;
use League\Fractal\TransformerAbstract;

class RewardTransformer extends TransformerAbstract
{
    /**
     * @var int
     */
    protected $total = 0;

    /**
     * Transform Reward model.
     *
     * @param \App\Models\Reward $reward
     *
     * @return array
     */
    public function transform(Reward $reward)
    {
        $discountAmount = $reward->getRewardProcessor()->getDiscountAmount($this->total);
        $discountPercent = $reward->getRewardProcessor()->getDiscountPercent($this->total);

        if ($discountAmount) {
            $discountStr = HAmount::fShort($discountAmount, $reward->partner->currency);
        } else {
            $discountStr = $discountPercent.'%';
        }

        if ($reward->title) {
            $discountStr = __('«%s»', $reward->title);
        }

        return [
            'id' => $reward->id,
            'title' => HReward::getTitleExhaustive($reward),
            'price' => floatval(\HReward::points($reward)),
            'price_type' => $reward->price_type,
            'discountAmount' => $discountAmount,
            'discountPercent' => $discountPercent,
            'discountStr' => $discountStr,
            'string' => HReward::getTitle($reward),
        ];
    }

    /**
     * Set total value.
     *
     * @param int $total
     *
     * @return static
     */
    public function setTotal(int $total)
    {
        $this->total = $total;

        return $this;
    }
}
