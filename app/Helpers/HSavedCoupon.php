<?php

use App\Models\SavedCoupon;

class HSavedCoupon
{
    public static function getStatusStr(SavedCoupon $savedCoupon)
    {
        switch ($savedCoupon->status) {
            case App\Models\SavedCoupon::STATUS_EXPIRED:
                return __('Expired');

            case App\Models\SavedCoupon::STATUS_NEW:
                return __('Redeem');

            case App\Models\SavedCoupon::STATUS_USED:
                return __('Used');

            case App\Models\SavedCoupon::STATUS_CANCELED:
                return __('Canceled');

            default:
                return __('Unknown');
        }
    }

    public static function canBeRedeemed(SavedCoupon $savedCoupon)
    {
        return App\Models\SavedCoupon::STATUS_NEW === $savedCoupon->status;
    }
}
