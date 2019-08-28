<?php

namespace App\Services\RewardProcessors;

use App\Exceptions\RewardAcquiringException;
use App\Models\Reward;
use App\Models\Transaction;
use App\Services\Giftd\ApiClient;

class GiftdDiscount extends Base
{
    protected function isExecutedOnAcquire(): bool
    {
        return true;
    }

    protected function isConfirmedOnExecute(): bool
    {
        return true;
    }

    protected function executeRewardInternal(Transaction $transaction): array
    {
        $giftdUserId = $this->getConfig(Reward::CONFIG_GIFTD_USER_ID);
        $giftdApiKey = $this->getConfig(Reward::CONFIG_GIFTD_API_KEY);

        if ($giftdUserId && $giftdApiKey) {
            $client = new ApiClient($giftdUserId, $giftdApiKey);
        } else {
            $client = ApiClient::create($transaction->user->partner);
        }

        $lifetime = $this->reward->config[Reward::CONFIG_LIFETIME] ?? 365 * 24 * 3600;

        if (isset($transaction->data[Transaction::DATA_LIFETIME_OVERRIDDEN])) {
            $lifetime = $transaction->data[Transaction::DATA_LIFETIME_OVERRIDDEN];
        }

        $result = $client->queryCrm('reward/getOrCreate', [
            'crm_user_key' => $transaction->user->key,
            'external_id' => 'crm-'.\App::environment().'/'.$transaction->id,
            'value' => $this->reward->value,
            'value_type' => $this->reward->value_type,
            'min_amount_total' => $this->reward->config[Reward::CONFIG_MIN_AMOUNT_TOTAL] ?? 0,
            'name' => $transaction->user->name,
            'lifetime' => $lifetime,
            'card_id' => $this->reward->getGiftdCardId(),
        ]);

        if (!isset($result['url'])) {
            throw new RewardAcquiringException('Malformed response received from GIFTD API: '.\HJson::encode($result));
        }

        return $result;
    }

    public function cancelPromoCode(Transaction $transaction)
    {
        $promoCode = $transaction->data[Transaction::DATA_PROMO_CODE] ?? null;

        if (!$promoCode) {
            throw new \RuntimeException("Unable to cancel GIFTD promo code, because transaction #{$transaction->id} data is empty: ".\HJson::encode($transaction->data->toArray()));
        }

        $client = ApiClient::create($transaction->user->partner);

        $data = $client->queryCrm('gift/cancel', [
            'token' => $promoCode,
        ]);

        $transaction->data = array_replace_recursive($transaction->data->toArray(), $data);
        $transaction->save();
    }
}
