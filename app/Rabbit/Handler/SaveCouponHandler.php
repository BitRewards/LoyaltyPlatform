<?php

namespace App\Rabbit\Handler;

use App\DTO\CredentialData;
use App\Mail\SaveCouponNotification;
use App\Models\Partner;
use App\Models\SavedCoupon;
use App\Models\Token;
use App\Models\User;
use App\Rabbit\Validator\SaveCouponRequestValidator;
use App\Rabbit\Validator\Traits\ValidatorAwareTrait;
use App\Services\PartnerService;
use App\Services\SavedCouponService;
use App\Services\SmsService;
use App\Services\TokenService;
use App\Services\UserService;
use GL\Rabbit\DTO\RPC\CRM\SaveCouponRequest;
use GL\Rabbit\DTO\RPC\CRM\SaveCouponResponse;
use GL\Rabbit\Validator\Exception\SaveCouponRequestValidatorException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SaveCouponHandler
{
    use ValidatorAwareTrait;

    /**
     * @var PartnerService
     */
    protected $partnerService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var SavedCouponService
     */
    protected $savedCouponService;

    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * @var Mail|\Mail
     */
    protected $mail;

    /**
     * @var Str
     */
    protected $str;

    /**
     * @var SmsService
     */
    protected $smsService;

    public function __construct(
        SaveCouponRequestValidator $requestValidator,
        PartnerService $partnerService,
        UserService $userService,
        SavedCouponService $savedCouponService,
        TokenService $tokenService,
        Mail $mail,
        Str $str,
        SmsService $smsService
    ) {
        $this->setValidator($requestValidator);

        $this->partnerService = $partnerService;
        $this->userService = $userService;
        $this->savedCouponService = $savedCouponService;
        $this->tokenService = $tokenService;
        $this->mail = $mail;
        $this->str = $str;
        $this->smsService = $smsService;
    }

    public function handle(SaveCouponRequest $request): SaveCouponResponse
    {
        \DB::beginTransaction();
        $this->getValidator()->validateOrFail($request);

        $partner = $this->partnerService->getByGiftdId($request->giftdPartnerId);
        \HLanguage::setLanguage($partner->default_language);

        $user = $this->getPartnerUserFromRequest($partner, $request);
        $newUserCredential = null;

        if (!$user) {
            $newUserCredential = $this->str::random(8);
            $user = $this->createPartnerUser($partner, $request, $newUserCredential);
        }

        $savedCoupon = $this->saveCoupon($user, $request);

        $result = new SaveCouponResponse(
            $this->redirectUrlResponse($user),
            $savedCoupon->id,
            $user->key,
            $user->referral_link,
            $user->referral_promo_code
        );

        \DB::commit();

        if ($savedCoupon->wasRecentlyCreated) {
            $this->notifyUser($savedCoupon, $newUserCredential);
        }

        \HLanguage::restorePreviousLanguage();

        return $result;
    }

    protected function getPartnerUserFromRequest(Partner $partner, SaveCouponRequest $request): ?User
    {
        $credential = $this->getCredentialFromRequest($partner->getAuthMethod(), $request);

        return $this->userService->findPartnerUser($partner, $credential);
    }

    protected function getCredentialFromRequest(string $authMethod, SaveCouponRequest $request): ?string
    {
        if (Partner::AUTH_METHOD_EMAIL === $authMethod) {
            return $request->email;
        }

        if (Partner::AUTH_METHOD_PHONE === $authMethod) {
            return $request->phone;
        }

        throw new \DomainException("Auth method '{$authMethod}' not supported");
    }

    protected function createPartnerUser(Partner $partner, SaveCouponRequest $request, string $password): User
    {
        $data = CredentialData::make([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $password,
            'signup_type' => User::SIGNUP_TYPE_SAVE_COUPON,
            'referrer_key' => $request->referrerKey,
        ]);

        return $this->userService->createClient($data, $partner);
    }

    protected function saveCoupon(User $user, SaveCouponRequest $request): SavedCoupon
    {
        $coupon = $this->savedCouponService->getPartnerCouponByCode($user->partner, $request->code);

        if (!$coupon) {
            $coupon = new SavedCoupon([
                'partner_id' => $user->partner->id,
                'user_id' => $user->id,
                'code' => $request->code,
                'discount_amount' => $request->discountAmount,
                'discount_percent' => $request->discountPercent,
                'discount_description' => $request->discountStr,
                'min_amount_total' => $request->minAmountTotal,
                'redeem_url' => $request->redeemUrl,
                'expired_at' => $request->expires,
            ]);

            $coupon->saveOrFail();
        } elseif ($coupon->user_id !== $user->id) {
            throw new SaveCouponRequestValidatorException(
                SaveCouponRequestValidatorException::COUPON_ALREADY_USE
            );
        }

        return $coupon;
    }

    protected function notifyUser(SavedCoupon $coupon, string $newUserCredential = null): void
    {
        $partner = $coupon->user->partner;

        if ($partner->isAuthMethodEmail()) {
            $this->notifyByEmail($coupon, $newUserCredential);
        } elseif ($partner->isAuthMethodPhone()) {
            $this->notifyByTextMessage($coupon, $newUserCredential);
        } else {
            throw new \RuntimeException('Notify user fail');
        }
    }

    protected function notifyByEmail(SavedCoupon $coupon, string $newUserCredential = null): void
    {
        $token = null;

        $token = $this->tokenService->createAutoLoginTokenForEmail($coupon->user);

        $redirectUrl = $this->embedRedirectUrl($coupon->user->partner, $token);

        $this->mail::send(new SaveCouponNotification($coupon, $redirectUrl, $newUserCredential));
    }

    protected function notifyByTextMessage(SavedCoupon $coupon, string $newUserCredential = null): void
    {
        $token = null;

        $token = $this->tokenService->createAutoLoginTokenForMobilePhone($coupon->user);

        $redirectUrl = $this->embedRedirectUrl($coupon->user->partner, $token);

        $message = __('Promo-code from %shop% is stored in your wallet: %url%', [
            'shop' => \HUrl::beautify($coupon->partner->url),
            'url' => $redirectUrl,
        ]);

        $this->smsService->send($coupon->user->phone, $message, true);
    }

    protected function redirectUrlResponse(User $user): string
    {
        $token = null;

        if ($user->wasRecentlyCreated) {
            $token = $this->tokenService->createAutoLoginTokenForWeb($user);
        }

        return $this->redirectUrl($user->partner, $token);
    }

    protected function embedRedirectUrl(Partner $partner, Token $token = null): string
    {
        $redirectUrl = $this->redirectUrl($partner, $token);

        return $this->partnerService->getEmbeddedUrl($partner, $redirectUrl);
    }

    protected function redirectUrl(Partner $partner, Token $token = null): string
    {
        $redirectUrl = route('client.index', [
            'partner' => $partner->key,
        ]);

        $redirectUrl .= $token ? "?__alt={$token->token}#balance" : '#login';

        return $redirectUrl;
    }
}
