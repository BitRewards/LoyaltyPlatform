<?php

namespace App\Transformers;

use HDate;
use HTransaction;
use App\Models\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * Transform Transaction model.
     *
     * @param \App\Models\Transaction $transaction
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        $expires = $transaction->data[Transaction::DATA_EXPIRES] ?? null;
        $expiresInSeconds = $expires ? ($expires - time()) : null;

        return [
            'id' => $transaction->id,
            'promoCode' => $promoCode = ($transaction->data[Transaction::DATA_PROMO_CODE] ?? null),
            'promoCodeExpires' => $expires,
            'promoCodeExpiresInSeconds' => $expiresInSeconds,
            'canPromoCodeBeApplied' => $promoCode && (!$expires || $expiresInSeconds > 0),
            'title' => HTransaction::getTitleExhaustive($transaction),
            'balanceChange' => $transaction->balance_change,
            'balanceChangeStr' => $transaction->balance_change >= 0
                    ? ('+'.floatval($transaction->balance_change))
                    : floatval($transaction->balance_change),
            'created' => HDate::dateTimeFull($transaction->created_at),
            'time' => HDate::time($transaction->created_at->getTimestamp()),
            'status' => $transaction->status,
            'statusStr' => HTransaction::getStatusStr($transaction),
            'actor_id' => $transaction->actor_id,
            'comment' => $comment = $transaction->data->comment ?? null,
            'confirmed' => HDate::dateTimeFull($transaction->confirmed_at),
            'orderStr' => HTransaction::getOrderStr($transaction),
            'bitrewardPayoutAmount' => $transaction->data->toArray()[Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT] ?? null,
            'originalOrderId' => $transaction->sourceStoreEvent && $transaction->sourceStoreEvent->converter_type ? $transaction->sourceStoreEvent->getConverter()->getOriginalOrderId() : null,
        ];
    }
}
