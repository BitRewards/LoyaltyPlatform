<?php

namespace App\Services\ActionProcessors;

use App\Models\Action;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Models\User;
use App\Services\Giftd\ApiClient;

class OrderReferral extends Base
{
    protected $requiresEntity = true;
    protected $requiresEntityConfirmation = true;

    public function getEntityType()
    {
        return StoreEntity::TYPE_ORDER;
    }

    protected function getTransactionReceiverUser(StoreEvent $event)
    {
        $entity = $event->entity;
        $refUserCrmKey = $entity->data->refUserCrmKey;

        if ($refUserCrmKey && $entity->data->userCrmKey && $refUserCrmKey == $entity->data->userCrmKey) {
            return null;
        }

        $user = null;

        if ($entity->data->promoCodes) {
            $promoCodes = $entity->data->promoCodes;

            if (!is_array($promoCodes)) {
                $promoCodes = [$promoCodes];
            }

            $firstPromoCode = mb_strtolower(trim($promoCodes[0]));

            if ($firstPromoCode) {
                $user = User::where('referral_promo_code', $firstPromoCode)
                            ->where('partner_id', $event->partner_id)
                            ->first();

                if (!$user && $event->partner->giftd_api_key) {
                    $apiClient = ApiClient::create($event->partner);

                    $result = $apiClient->check($firstPromoCode);

                    if ($result) {
                        $key = $result->crm_ref_user_key;

                        if ($key) {
                            $user = User::model()->findByKey($key);

                            if ($user && $user->partner_id == $event->partner_id) {
                                // to prevent unnecessary GIFTD API calls
                                $entity->data->refUserCrmKey = $key;
                                $entity->save();
                            }
                        }
                    }
                }
            }
        }

        if (!$user && $refUserCrmKey) {
            $user = User::model()->findByKey($refUserCrmKey);
        }

        $originalBuyer = parent::getTransactionReceiverUser($event);

        if (!$user) {
            if ($originalBuyer && $originalBuyer->referrer_id) {
                $user = $originalBuyer->referrer;
            }
        }

        if ($originalBuyer && $user && $originalBuyer->id === $user->id) {
            return null;
        }

        return $user;
    }

    public function getReferralRewardValue()
    {
        return $this->getSetting(Action::CONFIG_REFERRAL_REWARD_VALUE);
    }

    public function getReferralRewardValueType()
    {
        return $this->getSetting(Action::CONFIG_REFERRAL_REWARD_VALUE_TYPE);
    }

    public function getReferralRewardValueMinAmountTotal()
    {
        return $this->getSetting(Action::CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL);
    }

    public function getReferralRewardValueLifetime()
    {
        return $this->getSetting(Action::CONFIG_REFERRAL_REWARD_LIFETIME);
    }
}
