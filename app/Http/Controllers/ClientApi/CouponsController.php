<?php

namespace App\Http\Controllers\ClientApi;

use App\Http\Controllers\ClientApiController;
use App\Models\User;
use App\Services\CouponService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponsController extends ClientApiController
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var CouponService
     */
    protected $couponService;

    public function __construct(
        Auth $auth,
        CouponService $couponService
    ) {
        $this->auth = $auth;
        $this->couponService = $couponService;
    }

    public function userCouponList(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();
        $coupons = $this->couponService->getCoupons($user, ...$this->getPageParameters($request));
        $couponsCount = $this->couponService->getCouponsCount($user);

        return $this->responseJsonCollection($coupons, $couponsCount);
    }

    public function personCouponList(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();
        $coupons = $this->couponService->getPersonCoupons($user, ...$this->getPageParameters($request));
        $couponsCount = $this->couponService->getPersonCouponsCount($user);

        return $this->responseJsonCollection($coupons, $couponsCount);
    }
}
