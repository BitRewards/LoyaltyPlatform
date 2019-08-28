<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\DetachCodeRequest;
use App\Models\Code;
use App\Services\CodeService;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\AcquireCode;

class UserCardsController extends Controller
{
    /**
     * Attach new loyalty card to user.
     *
     * @param \App\Http\Requests\Api\AcquireCode $request
     * @param \App\Services\CodeService          $codeService
     * @param string                             $userKey
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AcquireCode $request, CodeService $codeService, $userKey)
    {
        $user = $request->getUserByKey();
        $code = $request->getCode();
        $codeService->acquire($user, $code);

        return response()->json(
            fractal($user, new UserTransformer())
        );
    }

    /**
     * Detaches given card from user (if possible) and rejects transactions.
     *
     * @param DetachCodeRequest $request
     * @param CodeService       $codeService
     * @param UserService       $userService
     * @param string            $userKey
     * @param string            $cardToken
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DetachCodeRequest $request, CodeService $codeService, UserService $userService, $userKey, $cardToken)
    {
        $user = $request->getUserByKey();
        $code = Code::where('partner_id', $request->user()->partner_id)
            ->where('token', $cardToken)
            ->first();

        if (is_null($code) || $code->user_id !== $user->id) {
            return response()->json([
                'error' => __('Given loyalty card was not found'),
            ], 404);
        }

        $codeService->detach($code, $user, $userService);

        return response()->json(
            fractal($user->fresh(), new UserTransformer())
        );
    }
}
