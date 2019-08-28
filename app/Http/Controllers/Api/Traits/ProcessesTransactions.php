<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\Transaction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ProcessesTransactions
{
    protected function serializeTransaction(Transaction $transaction)
    {
        $expires = $transaction->data[Transaction::DATA_EXPIRES] ?? null;
        $expiresInSeconds = $expires ? ($expires - time()) : null;

        return [
            'id' => $transaction->id,
            'promoCode' => $promoCode = ($transaction->data[Transaction::DATA_PROMO_CODE] ?? null),
            'promoCodeExpires' => $expires,
            'promoCodeExpiresInSeconds' => $expiresInSeconds,
            'canPromoCodeBeApplied' => $promoCode && (!$expires || $expiresInSeconds > 0),
            'title' => \HTransaction::getTitleExhaustive($transaction),
            'balanceChange' => $transaction->balance_change,
            'balanceChangeStr' => $transaction->balance_change >= 0 ? ('+'.(float) $transaction->balance_change) : (float) $transaction->balance_change,
            'created' => \HDate::dateTimeFull($transaction->created_at),
            'status' => $transaction->status,
            'statusStr' => \HTransaction::getStatusStr($transaction),
        ];
    }

    /**
     * @param $id
     *
     * @return Transaction
     */
    protected function retrieveTransaction($id)
    {
        $partner = \Auth::user()->partner;

        $transaction = Transaction::model()->whereAttributes([
            'partner_id' => $partner->id,
            'id' => $id,
        ])->first();

        if (!$transaction) {
            throw new NotFoundHttpException();
        }

        return $transaction;
    }
}
