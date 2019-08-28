<?php

namespace Bitrewards\ReferralTool\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function reject(Transaction $transaction)
    {
        $transaction->status = Transaction::STATUS_REJECTED;
        $transaction->saveOrFail();
    }

    public function confirm(Transaction $transaction)
    {
        $transaction->reward->getRewardProcessor()->executeReward($transaction, true);
    }
}
