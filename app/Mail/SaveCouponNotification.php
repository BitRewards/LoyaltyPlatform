<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\SavedCoupon;

class SaveCouponNotification extends UserNotification
{
    /**
     * @var SavedCoupon
     */
    protected $coupon;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $newUserCredential;

    public function __construct(SavedCoupon $coupon, string $redirectUrl, string $newUserCredential = null)
    {
        parent::__construct($coupon->user);

        $this->coupon = $coupon;
        $this->redirectUrl = $redirectUrl;
        $this->newUserCredential = $newUserCredential;
    }

    protected function getSubject(): string
    {
        if ($this->coupon->partner->isFiatReferralEnabled()) {
            return __('%discount% discount in %shop% is stored in referral program', [
                'discount' => $this->coupon->getDiscountFormatted(),
                'shop' => $this->coupon->partner->title,
            ]);
        }

        return __('%discount% discount in %shop% is stored in your personal wallet', [
            'discount' => $this->coupon->getDiscountFormatted(),
            'shop' => $this->coupon->partner->title,
        ]);
    }

    protected function getTemplateName(): string
    {
        return 'emails.save-coupon-notify';
    }

    protected function getTemplateVariables(): array
    {
        $expiredAt = null;

        if ($this->coupon->expired_at) {
            $expiredAt = \HDate::dateTimeFull($this->coupon->expired_at).' '
                .\HDate::getCurrentUserTimeZoneName();
        }

        return [
            'shop' => $this->coupon->partner->title,
            'discount' => $this->coupon->getDiscountFormatted(),
            'isNewUser' => null !== $this->newUserCredential,
            'login' => $this->user->email ?? $this->user->phone,
            'generatedPassword' => $this->newUserCredential,
            'couponExpireAt' => $expiredAt,
            'link' => $this->redirectUrl,
            'isFiatReferralEnabled' => $this->coupon->partner->isFiatReferralEnabled(),
        ];
    }
}
