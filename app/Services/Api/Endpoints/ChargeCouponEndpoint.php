<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class ChargeCouponEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/coupons/charge';
    }

    /**
     * HTTP POST method operation.
     *
     * @return ApiOperation
     */
    public function post()
    {
        return new ApiOperation([
            'method' => 'POST',
            'summary' => __('Redeem Coupon'),
            'description' => __('This method allows to redeem the coupon code (i.e. charge it and invalidate it)'),
            'parameters' => [
                $this->stringInput('token', __('Unique coupon code, provided by the user'))->required(),
                $this->floatInput('amount_total', __('Total amount of the purchase (purchase value / order value) before the coupon discount subtraction'))->required(),
                $this->stringInput('comment', __('Comment to redemption')),
                $this->stringInput('client_ip', __('IP address of the user, who sent the coupon code. Recommended parameter.')),
            ],
            'tags' => [__('Coupons')],
            'responses' => [
                $this->jsonItem(__('Coupon Data'), 'Coupon'),
                $this->jsonError(__('The coupon code is already used').' (errorCode = tokenAlreadyUsed)', 422),
                $this->jsonError(__('Your account is temporarily blocked for making too many wrong coupon code check attempts').' (errorCode = yourAccountIsBanned)', 429),
                $this->jsonError(__('An error occurred, please check errorCode'), 422),
            ],
        ]);
    }
}
