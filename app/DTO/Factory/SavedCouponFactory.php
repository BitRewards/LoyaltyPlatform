<?php

namespace App\DTO\Factory;

use App\DTO\PartnerPage\SavedCouponData as PartnerPageSavedCouponData;
use App\DTO\SavedCouponData;
use App\Models\SavedCoupon;
use App\Models\User;
use App\Services\SavedCouponService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class SavedCouponFactory
{
    /**
     * @var SavedCouponService
     */
    protected $savedCouponService;

    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var \HDate
     */
    protected $dateHelper;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HSavedCoupon
     */
    protected $savedCouponHelper;

    public function __construct(
        SavedCouponService $savedCouponService,
        PartnerFactory $partnerFactory,
        \HDate $dateHelper,
        UrlGenerator $urlGenerator,
        \HSavedCoupon $savedCouponHelper
    ) {
        $this->savedCouponService = $savedCouponService;
        $this->partnerFactory = $partnerFactory;
        $this->dateHelper = $dateHelper;
        $this->urlGenerator = $urlGenerator;
        $this->savedCouponHelper = $savedCouponHelper;
    }

    public function factoryUserSavedCoupons(User $user, int $page = 1, int $perPage = 20): Collection
    {
        $savedCoupons = $this->savedCouponService->getUserCoupons($user, $page, $perPage);

        return $this->factoryCollection($savedCoupons);
    }

    public function factoryPartnerPageUserSavedCoupons(User $user, int $page = 1, int $perPage = 20): Collection
    {
        $savedCoupons = $this->factoryUserSavedCoupons(...\func_get_args());
        $partner = $user->partner;

        return $savedCoupons->map(function (SavedCouponData $coupon) use ($partner) {
            $savedCoupon = PartnerPageSavedCouponData::make($coupon->toArray());
            $savedCoupon->modalUrl = $this->urlGenerator->route('client.savedCoupon.usageModal', [
                'partner' => $partner->key,
                'savedCoupon' => $coupon->id,
            ]);
            $savedCoupon->created = $this->dateHelper::dateTime($coupon->createdAt);

            if ($coupon->expiredAt) {
                $savedCoupon->expired = $this->dateHelper::dateTime($coupon->expiredAt);
            }

            return $savedCoupon;
        });
    }

    public function factory(SavedCoupon $savedCoupon): SavedCouponData
    {
        return SavedCouponData::make([
            'id' => $savedCoupon->id,
            'partner' => $this->partnerFactory->factory($savedCoupon->partner),
            'code' => $savedCoupon->code,
            'status' => $savedCoupon->status,
            'statusStr' => $this->savedCouponHelper::getStatusStr($savedCoupon),
            'canBeRedeemed' => \HSavedCoupon::canBeRedeemed($savedCoupon),
            'discountAmount' => $savedCoupon->discount_amount,
            'discountPercent' => $savedCoupon->discount_percent,
            'discountDescription' => $savedCoupon->discount_description,
            'discountFormatted' => $savedCoupon->getDiscountFormatted(),
            'minAmountTotal' => $savedCoupon->min_amount_total,
            'redeemUrl' => $savedCoupon->redeem_url,
            'createdAt' => $savedCoupon->created_at->timestamp,
            'expiredAt' => $savedCoupon->expired_at ? $savedCoupon->expired_at->timestamp : null,
        ]);
    }

    /**
     * @param Collection $collection
     *
     * @return Collection|SavedCouponData[]
     */
    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (SavedCoupon $savedCoupon) {
            return $this->factory($savedCoupon);
        });
    }
}
