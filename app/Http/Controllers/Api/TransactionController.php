<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\Api\ListTransactions;
use App\Transformers\TransactionTransformer;
use App\Services\RewardProcessors\GiftdDiscount;
use App\Http\Controllers\Api\Traits\ProcessesTransactions;

class TransactionController extends ApiController
{
    use ProcessesTransactions;

    /**
     * Show partner transactions.
     *
     * @param \App\Http\Requests\Api\ListTransactions $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListTransactions $request)
    {
        $transactions = $this->applyGlobalFilters(
            $request,
            Transaction::where('partner_id', $request->user()->partner->id)
        );

        return response()->json(
            fractal($transactions->paginate(), new TransactionTransformer())
        );
    }

    /**
     * Show transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $transactionId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $transactionId)
    {
        $transaction = $this->retrieveTransaction($transactionId);

        return response()->json(
            fractal($transaction, new TransactionTransformer())
        );
    }

    /**
     * Cancel promo code.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $transactionId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPromoCode(Request $request, $transactionId)
    {
        $transaction = $this->retrieveTransaction($transactionId);

        if (!$transaction->reward) {
            return response()->json([
                'error' => __('Linked Reward was not found'),
            ], 404);
        }

        $processor = $transaction->reward->getRewardProcessor();

        if (!$processor instanceof  GiftdDiscount) {
            return response()->json([
                'error' => __('Linked Reward is not a Giftd Reward'),
            ], 422);
        }

        $processor->cancelPromoCode($transaction);

        return jsonResponse('ok');
    }
}
