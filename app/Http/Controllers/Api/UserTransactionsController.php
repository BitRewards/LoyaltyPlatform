<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;

class UserTransactionsController extends ApiController
{
    /**
     * Show user transactions list.
     *
     * @param Request $request
     * @param $userKey
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $userKey)
    {
        $user = User::where('partner_id', $request->user()->partner->id)
                ->where('key', $userKey)
                ->first();

        if (is_null($user)) {
            return response()->json([
                'error' => __('User was not found'),
            ], 404);
        }

        $transactions = $user->transactions()->paginate();

        return response()->json(
            fractal($transactions, new TransactionTransformer())
        );
    }
}
