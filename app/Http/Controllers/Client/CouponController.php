<?php

namespace App\Http\Controllers\Client;

use App\DTO\Factory\SavedCouponFactory;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\SavedCoupon;

class CouponController extends Controller
{
    /**
     * @var SavedCouponFactory
     */
    private $savedCouponFactory;

    public function __construct(SavedCouponFactory $savedCouponFactory)
    {
        $this->savedCouponFactory = $savedCouponFactory;
    }

    public function savedCouponModal(Partner $partner, SavedCoupon $coupon)
    {
        $savedCoupon = $this->savedCouponFactory->factory($coupon);

        return view('loyalty/_saved-coupon-usage-modal', [
            'savedCoupon' => $savedCoupon,
        ]);
    }
}
