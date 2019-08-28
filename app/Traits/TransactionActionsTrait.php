<?php

namespace App\Traits;

use App\Models\Reward;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;

trait TransactionActionsTrait
{
    public function confirm($id)
    {
        /**
         * @var Transaction
         */
        $transaction = Transaction::find($id);

        try {
            if ($transaction->reward_id && (Reward::TYPE_FIAT_WITHDRAW === $transaction->reward->type)) {
                $transaction->reward->getRewardProcessor()->executeReward($transaction);
            } elseif (!app(TransactionService::class)->forceConfirm($transaction)) {
                throw new \Exception();
            }

            \Alert::success(__('Transaction confirmed!'))->flash();
        } catch (\Exception $e) {
            \Alert::error(__('Unable to confirm transaction'))->flash();
        }

        return redirect()->back();
    }

    public function reject($id): RedirectResponse
    {
        $transaction = Transaction::find($id);

        if (app(TransactionService::class)->forceReject($transaction)) {
            \Alert::success(__('Transaction declined!'))->flash();
        } else {
            \Alert::warning(__('Unable to decline transaction'))->flash();
        }

        return redirect()->back();
    }
}
