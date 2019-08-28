<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ProcessesTransactions;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\BalanceCallbackRequest;
use App\Http\Requests\Api\EthTransferCallbackRequest;
use App\Http\Requests\Api\TokenTransferCallbackRequest;
use App\Http\Requests\Api\TransactionCallbackRequest;
use App\Jobs\HandleTreasuryCallback;
use App\Mail\BitBalanceAlert;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;

class TreasuryController extends ApiController
{
    use ProcessesTransactions;

    protected function _confirmTransaction(Transaction $transaction)
    {
        return app(TransactionService::class)->forceConfirm($transaction);
    }

    protected function _rejectTransaction(Transaction $transaction)
    {
        return app(TransactionService::class)->forceReject($transaction);
    }

    /**
     * Show transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $transactionId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionCallback(TransactionCallbackRequest $request, $transactionId)
    {
        $transaction = $this->retrieveTransaction($transactionId);

        if (!$transaction) {
            return response()->json(
                ['status' => 'failed'], 404
            );
        }

        dispatch(new HandleTreasuryCallback('withdraw', $request->input(), ['transactionId' => $transaction->id]));

        return response()->json(
            ['status' => 'ok']
        );
    }

    public function ethTransferCallback(EthTransferCallbackRequest $request)
    {
        dispatch(new HandleTreasuryCallback('eth-transfer', $request->input(), []));

        return response()->json(
            ['status' => 'ok']
        );
    }

    public function tokenCallback(TokenTransferCallbackRequest $request)
    {
        if (!$request->user()->partner_id) {
            return response()->json(
                ['status' => 'failed'], 400
            );
        }

        dispatch(new HandleTreasuryCallback('token-transfer', $request->input(), [
            'partnerId' => $request->user()->partner_id,
        ]));

        return response()->json(
            ['status' => 'ok']
        );
    }

    public function balanceCallback(BalanceCallbackRequest $request)
    {
        /* @var User $user */
        $user = $request->user();

        if ($user->partner_id) {
            \Mail::later(5, new BitBalanceAlert($user->partner->mainAdministrator));
        }

        return response()->json(
            ['status' => 'ok']
        );
    }
}
