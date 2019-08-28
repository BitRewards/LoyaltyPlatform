<?php

namespace App\Services\Giftd;

use App\Models\Partner;

class GiftdService
{
    /**
     * @param Partner $partner
     *
     * @return bool|null
     */
    public function updateReferralWidget(Partner $partner)
    {
        $actionProcessor = $partner->getOrderReferralActionProcessor();

        if (!$actionProcessor || !$partner->isConnectedToGiftdApi()) {
            return null;
        }

        try {
            $client = ApiClient::create($partner);

            $client->queryCrm('referral/updateReferralWidget', [
                'crm_action_id' => $actionProcessor->getAction()->id,
                'value' => $actionProcessor->getReferralRewardValue(),
                'value_type' => $actionProcessor->getReferralRewardValueType(),
                'min_amount_total' => $actionProcessor->getReferralRewardValueMinAmountTotal(),
                'lifetime' => $actionProcessor->getReferralRewardValueLifetime(),
                'card_id' => $actionProcessor->getAction()->getGiftdCardId(),
            ]);

            return true;
        } catch (\Throwable $e) {
        }

        return null;
    }
}
