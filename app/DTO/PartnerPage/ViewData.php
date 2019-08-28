<?php

namespace App\DTO\PartnerPage;

use App\Models\Partner;

class ViewData
{
    /**
     * @var Partner
     */
    public $partner;

    /**
     * @var string
     */
    public $cabinetTitle;

    /**
     * @var string
     */
    public $earnMessage;

    /**
     * @var string
     */
    public $earnTipMessage;

    /**
     * @var string
     */
    public $spendMessage;

    /**
     * @var string
     */
    public $spendTipMessage;

    /**
     * @var string
     */
    public $discountInsteadOfLoyaltyMessage;

    /**
     * @var string
     */
    public $rewardNAmountMessage;

    /**
     * @var bool
     */
    public $activatePlasticBeforeOtherActions;

    /**
     * @var string
     */
    public $emailConfirmationUrl;

    /**
     * @var string
     */
    public $provideEmailUrl;

    /**
     * @var string
     */
    public $logoutUrl;

    /**
     * @var string
     */
    public $resetPasswordEmailOrPhone;

    /**
     * @var string
     */
    public $resetPasswordConfirmToken;

    /**
     * @var string
     */
    public $userPhoneConfirmationMessage;

    /**
     * @var string
     */
    public $ethToBitExchangeRate;

    /**
     * @var string
     */
    public $bitToEthExchangeRate;

    /**
     * @var string
     */
    public $updateEthWalletAddressUrl;

    /**
     * @var bool
     */
    public $isEarnBitHidden;

    /**
     * @var bool
     */
    public $isSpendBitHidden;

    /**
     * @var bool
     */
    public $isInviteFriendsHidden;

    /**
     * @var bool
     */
    public $isActivatePlasticHidden;

    /**
     * @var bool
     */
    public $isMyCouponsHidden;

    /**
     * @var bool
     */
    public $isLogoutButtonHidden;

    /**
     * @var bool
     */
    public $isPopupCloseButtonHidden;

    /**
     * @var bool
     */
    public $isEditProfileButtonHidden;

    /**
     * @var bool
     */
    public $isClientReferralHeadingHidden;

    /**
     * @var bool
     */
    public $isEnterPromocodeHidden;

    /**
     * @var bool
     */
    public $isWithdrawDisabled;

    /**
     * @var bool
     */
    public $customSocialActionHasUrl;

    /**
     * @var bool
     */
    public $customSocialActionHasImage;
}
