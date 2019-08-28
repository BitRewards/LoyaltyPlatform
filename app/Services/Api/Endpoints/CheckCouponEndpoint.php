<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class CheckCouponEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/coupons/check';
    }

    /**
     * HTTP GET method operation.
     *
     * @return ApiOperation
     */
    public function get()
    {
        return new ApiOperation([
            'method' => 'GET',
            'summary' => __('Check Coupon'),
            'description' => __('Looks up given coupon code and returns discount settings. Check "isAvailable" response field and give the customer the discount of either "amount" or "discountPercent" with the condition of "minAmountTotal" minimum order total. At least one of that fields will have non-null value.'),
            'parameters' => [
                $this->stringQuery('token', __('Coupon code'))->required(),
                $this->floatInput('amount_total', __('Total amount of order to which the coupon code is applied')),
                $this->stringInput('client_ip', __('IP address of the user, who sent the coupon code. Recommended parameter.')),
            ],
            'tags' => [__('Coupons'), __('Search')],
            'responses' => [
                $this->jsonItem(__('Coupon Data. If the coupon code is not found, null value is returned.'), 'Coupon'),
                $this->jsonError(__('Your account is temporarily blocked for making too many wrong coupon code check attempts').' (errorCode = yourAccountIsBanned)', 429),
                $this->jsonError(__('An error occurred, please check errorCode'), 422),
            ],
        ]);
    }
}
