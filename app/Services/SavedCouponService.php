<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\SavedCoupon;
use App\Models\User;
use Illuminate\Support\Collection;

class SavedCouponService
{
    /**
     * @var SavedCoupon
     */
    protected $savedCouponModel;

    public function __construct(SavedCoupon $savedCouponModel)
    {
        $this->savedCouponModel = $savedCouponModel;
    }

    public function getPartnerCouponByCode(Partner $partner, string $code): ?SavedCoupon
    {
        return $this->savedCouponModel::wherePartnerId($partner->id)->whereCode($code)->first();
    }

    public function getUserCoupons(User $user, int $page = 1, int $perPage = 20): Collection
    {
        return $this->savedCouponModel::whereUserId($user->id)->forPage($page, $perPage)->get();
    }
}
