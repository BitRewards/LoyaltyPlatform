<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionOutput;
use Illuminate\Support\Collection;

class TransactionOutputService
{
    public function getConfirmedOutputsByOutputTransactions(Transaction $toTransaction): Collection
    {
        return TransactionOutput::where('transaction_to_id', $toTransaction->id)
            ->where('status', TransactionOutput::STATUS_CONFIRMED)
            ->get();
    }

    public function getConfirmedOutputsByInputTransactions(Transaction $fromTransaction): Collection
    {
        return TransactionOutput::where('transaction_from_id', $fromTransaction->id)
            ->where('status', TransactionOutput::STATUS_CONFIRMED)
            ->get();
    }

    public function getBurnedTransactionOutput(Transaction $expiredTransaction): ?TransactionOutput
    {
        return TransactionOutput::where('transaction_to_id', $expiredTransaction->id)
            ->where('status', TransactionOutput::STATUS_CONFIRMED)
            ->first();
    }
}
