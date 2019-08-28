<?php

namespace App\DTO\PartnerPage;

use App\Models\Credential;
use App\Models\Partner;
use App\Models\User;
use App\Services\Fiat\FiatService;
use App\Services\UserService;

class UserDataFactory
{
    /**
     * @var UserCodeDataFactory
     */
    protected $userCodeDataFactory;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var \HUser
     */
    protected $userHelper;

    /**
     * @var \HStr
     */
    protected $stringHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var FiatService
     */
    protected $fiatService;

    public function __construct(
        UserCodeDataFactory $userCodeDataFactory,
        UserService $userService,
        \HUser $userHelper,
        \HStr $stringHelper,
        \HAmount $amountHelper,
        FiatService $fiatService
    ) {
        $this->userCodeDataFactory = $userCodeDataFactory;
        $this->userService = $userService;
        $this->userHelper = $userHelper;
        $this->stringHelper = $stringHelper;
        $this->amountHelper = $amountHelper;
        $this->fiatService = $fiatService;
    }

    public function factory(User $user, Partner $partner): UserData
    {
        $userData = new UserData();
        $userData->viewData = new UserViewData();

        $userData->name = $user->name;
        $emailCredential = $user->person->credentials->where('type_id', '=', Credential::TYPE_EMAIL)->first();
        $userData->email = $emailCredential ? $emailCredential->email : null;
        $phoneCredential = $user->person->credentials->where('type_id', '=', Credential::TYPE_PHONE)->first();
        $userData->phone = $phoneCredential ? $phoneCredential->phone : null;
        $userData->avatar = $this->userHelper::getPictureOrPlaceholder($user);
        $userData->referralLink = $user->referral_link;
        $userData->referralPromoCode = $user->referral_promo_code;
        $userData->bitTokenSenderAddress = $user->bit_tokens_sender_address;
        $userData->balanceAmount = $user->balance;
        $userData->balanceAmountPercent = \HAmount::pointsToPercentFormatted($user->balance);

        if ($partner->isBitrewardsEnabled()) {
            $userData->currency = 'BIT';
        } else {
            $userData->currency = __('{point|points}', ['count' => (int) $user->balance]);
        }

        $userData->isUserWithoutEmailOrPhone = $this->userService->isUserWithoutConfirmedEmailOrPhone($user);
        $userData->isUserConfirmed = $this->userService->isUserConfirmed($user);

        $userData->codes = $this->userCodeDataFactory->factory($user);

        $userData->viewData->userPhoneConfirmationMessage = $user->phone
            ? __('Verification code sent on %s', $this->stringHelper::mask($user->phone))
            : __('Confirmation code is sent to you through SMS');

        $partnerCurrency = $this->amountHelper::sISO4217($partner->currency);
        $exchangeRate = $this->fiatService->getExchangeRate('BIT', $partnerCurrency);
        $userBalanceInPartnerCurrencyAmount = $this->amountHelper::round($user->balance * $exchangeRate, 2);
        $userData->balanceInPartnerCurrency = $this->amountHelper::fSign(
            $userBalanceInPartnerCurrencyAmount,
            $partner->currency
        );

        $userData->ethSenderAddress = $user->eth_sender_address;
        $userData->key = $user->key;

        return $userData;
    }
}
