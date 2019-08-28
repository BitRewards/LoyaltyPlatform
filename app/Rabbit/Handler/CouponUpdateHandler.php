<?php

namespace App\Rabbit\Handler;

use App\Models\SavedCoupon;
use App\Services\PartnerService;
use App\Services\SavedCouponService;
use GL\Rabbit\DTO\Events\CouponUpdate;

class CouponUpdateHandler
{
    /**
     * @var PartnerService
     */
    protected $partnerService;

    /**
     * @var SavedCouponService
     */
    protected $savedCouponService;

    public function __construct(
        PartnerService $partnerService,
        SavedCouponService $savedCouponService
    ) {
        $this->partnerService = $partnerService;
        $this->savedCouponService = $savedCouponService;
    }

    public function handle(CouponUpdate $event)
    {
        \DB::transaction(function () use ($event) {
            $partner = $this->partnerService->getByGiftdId($event->giftdPartnerId);

            if (!$partner) {
                return;
            }

            $savedCoupon = $this->savedCouponService->getPartnerCouponByCode($partner, $event->code);

            if (!$savedCoupon) {
                return;
            }

            $statusMatching = [
                CouponUpdate::STATUS_READY => SavedCoupon::STATUS_NEW,
                CouponUpdate::STATUS_CANCELED => SavedCoupon::STATUS_CANCELED,
                CouponUpdate::STATUS_USED => SavedCoupon::STATUS_USED,
                CouponUpdate::STATUS_EXPIRED => SavedCoupon::STATUS_EXPIRED,
            ];

            $savedCouponStatus = $statusMatching[$event->status] ?? null;

            if (!$savedCouponStatus) {
                throw new \RuntimeException("Unknown CouponUpdate status: {$event->status} for code {$event->code}");
            }

            $savedCoupon->status = $savedCouponStatus;
            $savedCoupon->saveOrFail();
        });
    }
}
