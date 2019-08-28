<?php

namespace App\Services\RewardProcessors;

use App\Exceptions\RewardAcquiringException;
use App\Models\Transaction;

class FiatWithdraw extends Base
{
    protected function isExecutedOnAcquire(): bool
    {
        return false;
    }

    protected function isConfirmedOnExecute(): bool
    {
        return true;
    }

    protected function executeRewardInternal(Transaction $transaction): array
    {
        try {
            $partner = $transaction->partner;
            $balanceBefore = $balanceAfter = $partner->balance;
            $balanceAfter -= $transaction->getFiatWithdrawAmount(0);
            $balanceAfter -= $transaction->getFiatWithdrawFeeValue(0);

            $partner->balance = $balanceAfter;
            $partner->saveOrFail();
        } catch (\Exception $e) {
            throw new RewardAcquiringException($e->getMessage());
        }

        $result = [
            Transaction::DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_BEFORE => $balanceBefore,
            Transaction::DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_AFTER => $balanceAfter,
        ];

        return $result;
    }
}
