<?php

namespace App\Http\Controllers\Api;

use App\Models\Partner;
use App\Services\Giftd\ApiClient;
use App\Http\Controllers\Controller;
use App\Services\Giftd\ApiException;
use App\Transformers\CouponTransformer;
use App\Http\Requests\Api\ChargeGiftdCoupon;

class CouponController extends Controller
{
    /**
     * Charge coupon.
     *
     * @param \App\Http\Requests\Api\ChargeGiftdCoupon $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function charge(ChargeGiftdCoupon $request)
    {
        $partner = $request->user()->partner;
        /*
         * @var Partner
         */
        if (!$partner->isConnectedToGiftdApi()) {
            return response()->json([
                'error' => __('Your account is not connected to the Giftd API'),
            ], 422);
        }

        $client = ApiClient::create($partner);

        try {
            $card =
                $client
                    ->withClientIp(trim($request->input('client_ip')) ?: null)
                    ->charge($request->input('token'), null, $request->input('amount_total'), null, $request->input('comment'));
        } catch (ApiException $e) {
            switch ($e->code) {
                case ApiClient::ERROR_TOKEN_ALREADY_USED:
                    return response()->json([
                        'error' => __('The coupon code is already used'),
                        'errorCode' => $e->code,
                    ], 422);

                    break;

                case ApiClient::ERROR_YOUR_ACCOUNT_IS_BANNED:
                    return response()->json([
                        'error' => __('Your account is temporarily blocked for making too many wrong coupon code check attempts'),
                        'errorCode' => $e->code,
                    ], 429);

                    break;

                default:
                    return response()->json([
                        'error' => __('An error occurred, please check errorCode'),
                        'errorCode' => $e->code,
                    ], 422);

                    break;
            }
        }

        return response()->json(
            fractal($card, (new CouponTransformer())->setPartner($partner))
        );
    }
}
