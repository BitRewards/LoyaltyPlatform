<?php

namespace App\Services\Api\Definitions;

class CouponDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Coupon';
    }

    /**
     * Get the array representation of defintion.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'object',
            'properties' => [
                'isAvailable' => ['type' => 'boolean', 'description' => __('Indicates whether this coupon is not used, expired or invalidated')],
                'amount' => ['type' => 'number', 'description' => __("Discount amount (in the shop's currency)")],
                'discountPercent' => ['type' => 'number', 'description' => __('Discount percent')],
                'minAmountTotal' => ['type' => 'number', 'description' => __('Minimum total amount')],
                'minAmountTotalStr' => ['type' => 'string', 'description' => __('Formatted minimum total amount')],
                'isFree' => ['type' => 'boolean', 'description' => __('Indicated whether this coupon is a free promo code or a paid gift card')],
                'expires' => ['type' => 'integer', 'description' => __('UNIX timestamp of coupon expiration time')],
                'expiresStr' => ['type' => 'string', 'description' => __('Formatted coupon expiration time')],
                'token' => ['type' => 'string', 'description' => __('Coupon code')],
                'discountFormatted' => ['type' => 'string', 'description' => __('Formatted discount')],
                'userKey' => ['type' => 'string', 'description' => __("If this coupon code was acquired as a reward in the BitRewards Loyalty App, userKey contains a 'key' of the user who acquired this coupon")],
                'rewardStr' => ['type' => 'string', 'description' => __('If this coupon code was acquired as a reward in the BitRewards Loyalty App, this field contains a brief title of that reward')],
            ],
            'required' => ['isAvailable'],
        ];
    }
}
