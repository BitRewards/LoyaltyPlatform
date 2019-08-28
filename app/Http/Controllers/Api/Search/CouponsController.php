<?php

namespace App\Http\Controllers\Api\Search;

use App\Models\Partner;
use App\Services\Giftd\ApiException;
use Illuminate\Http\Request;
use App\Services\Giftd\ApiClient;
use App\Http\Controllers\Controller;
use App\Transformers\CouponTransformer;

class CouponsController extends Controller
{
    /**
     * Find gift card.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = trim($request->input('token', ''));

        if (!$query) {
            return jsonResponse(null);
        }

        $amountTotal = floatval($request->input('amount_total'));

        $partner = $request->user()->partner;

        /*
         * @var Partner
         */
        if (!$partner->isConnectedToGiftdApi()) {
            return jsonResponse(null);
        }

        $client = ApiClient::create($partner);

        try {
            $card =
                $client
                    ->withClientIp(trim($request->input('client_ip')) ?: null)
                    ->check($query, null, $amountTotal);
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
                        'error' => __('Your account is temporarily blocked for making too many requests with wrong coupon codes'),
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

        if (!$card) {
            return jsonResponse(null);
        }

        return response()->json(
            fractal($card, (new CouponTransformer())->setPartner($partner))
        );
    }
}
