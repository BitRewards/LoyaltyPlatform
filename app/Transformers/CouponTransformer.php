<?php

namespace App\Transformers;

use HDate;
use HAmount;
use HReward;
use App\Models\Partner;
use App\Models\Transaction;
use App\Services\Giftd\Card;
use League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    /**
     * @var \App\Models\Partner
     */
    protected $partner;

    /**
     * Transform Card model.
     *
     * @param \App\Services\Giftd\Card $card
     *
     * @return array
     */
    public function transform(Card $card)
    {
        $result = [
            'isAvailable' => (bool) $card->is_available,
            'amount' => (float) $card->amount_available ?: null,
            'minAmountTotal' => (float) $card->min_amount_total ?: null,
            'discountPercent' => (float) $card->discount_percent ?: null,

            'isFree' => (bool) $card->is_free,

            'isAmountTotalRequired' => $card->amount_total_required,

            'minAmountTotalStr' => HAmount::fSign($card->min_amount_total, $this->partner->currency),
            'expires' => (int) $card->expires,
            'expiresStr' => HDate::dateTimeFull($card->expires),
            'token' => $card->token,
            'discountFormatted' => $card->discount_formatted,
            'userKey' => $card->crm_user_key,
        ];

        $transaction = Transaction::model()->findByPromoCode($this->partner, $card->token);

        if ($transaction) {
            $result['rewardStr'] = HReward::getTitleExhaustive($transaction->reward);
        }

        return $result;
    }

    /**
     * Set the partner.
     *
     * @param \App\Models\Partner $partner
     *
     * @return static
     */
    public function setPartner(Partner $partner)
    {
        $this->partner = $partner;

        return $this;
    }
}
