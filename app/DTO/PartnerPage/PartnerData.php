<?php

namespace App\DTO\PartnerPage;

use App\DTO\PartnerSettingsData;
use App\Models\Credential;
use App\Models\Partner;
use App\Models\User;

class PartnerData
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $ethAddress;

    /**
     * @var string
     */
    public $signUpUrl;

    /**
     * @var string
     */
    public $authMethod;

    /**
     * @var string
     */
    public $isBitRewardEnabled;

    /**
     * @var string
     */
    public $isBitRewardsBrandForced;

    /**
     * @var int
     */
    public $currencyId;

    /**
     * @var PartnerSettingsData
     */
    public $settings;

    /**
     * @var bool
     */
    public $isOrderReferralActionExist;

    /**
     * @var string
     */
    public $clientEventProcessorUrl;

    /**
     * @var string
     */
    public $clientReferralHeading;

    /**
     * @var string
     */
    public $clientReferralSubtitle;

    /**
     * @var string
     */
    public $clientReferralMinAmountNotification;

    /**
     * @var string
     */
    public $referralRewardMessage;

    /**
     * @var bool
     */
    public $discountInsteadOfLoyalty;

    /**
     * @var float
     */
    public $signUpBonusAmount;

    /**
     * @var string
     */
    public $signUpBonus;

    /**
     * @var string
     */
    public $fiatReferralBonus;

    /**
     * @var string
     */
    public $oauthFBUrl;

    /**
     * @var string
     */
    public $oauthVKUrl;

    /**
     * @var string
     */
    public $twitterAuthUrl;

    /**
     * @var string
     */
    public $oauthGoogleUrl;

    /**
     * @var string
     */
    public $primaryColor;

    /**
     * @var bool
     */
    public $isMenuFontBolder;

    /**
     * @var string
     */
    public $clientSupportUrl;

    /**
     * @var string
     */
    public $clientEventAcquireCodeUrl;

    /**
     * @var string
     */
    public $clientRewardBitRewardsPayoutUrl;

    /**
     * @var string
     */
    public $clientRewardConfirmDepositUrl;

    /**
     * @var string
     */
    public $clientConfirmPhoneUrl;

    /**
     * @var string
     */
    public $clientProvidePhoneUrl;

    /**
     * @var string
     */
    public $resetPasswordByRequestUrl;

    /**
     * @var string
     */
    public $clientResetPasswordUrl;

    /**
     * @var float|int
     */
    public $brwToUsdRate;

    /**
     * @var float|int
     */
    public $brwAmount;

    /**
     * @var string
     */
    public $withdrawFeeType;

    /**
     * @var string
     */
    public $withdrawFeeValue;

    /**
     * @var float|int
     */
    public $withdrawAmountMin;

    /**
     * @var float|int
     */
    public $withdrawAmountMax;

    /**
     * @var PartnerSettingsData
     */
    public $partnerSettings;

    /**
     * @var string
     */
    public $updateWalletAddressUrl;
    /**
     * @var string
     */
    public $mergeByEmailUrl;

    /**
     * @var string
     */
    public $mergeByPhoneUrl;

    /**
     * @var string
     */
    public $checkEmailIsConfirmedUrl;

    /**
     * @var string
     */
    public $inviteUrl;

    /**
     * @var string
     */
    public $checkEmailUrl;

    /**
     * @var string
     */
    public $checkPhoneUrl;

    /**
     * @var string
     */
    public $forgotUrl;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var string
     */
    public $supportUrl;

    /**
     * @var string
     */
    public $getConfirmationStatusUrl;

    /**
     * @var string
     */
    public $sendPhoneConfirmationUrl;

    /**
     * @var string
     */
    public $sendMergeByPhoneConfirmationUrl;

    /**
     * @var string
     */
    public $addEmailUrl;

    /**
     * @var string
     */
    public $addPhoneUrl;

    /**
     * @var string
     */
    public $confirmEmailUrl;

    /**
     * @var string
     */
    public $confirmPhoneUrl;

    /**
     * @var string
     */
    public $sendMergeByEmailConfirmationUrl;

    /**
     * @var string
     */
    public $fiatWithdrawUrl;

    /**
     * @var bool
     */
    public $isFiatReferrerEnabled;

    /**
     * @var bool
     */
    public $isMergeBalancesEnabled;

    /**
     * @var bool
     */
    public $isReferralPromoCodeEnabled;

    /**
     * @var bool
     */
    public $isReferralLinkEnabled;

    /**
     * @var string
     */
    public $referrerStatisticUrl;

    /**
     * @var bool
     */
    public $isOborotPromoPartner;

    /**
     * @var
     */
    public $isFightwearPromoPartner;

    /**
     * @var bool
     */
    public $isAuthViaSocialNetworksHidden;

    /**
     * @var bool
     */
    public $isBazelevsPartner;

    /**
     * @var bool
     */
    public $isAvtocodPartner;

    /**
     * @var bool
     */
    public $isMigoff;

    /**
     * @var string
     */
    public $fiatWithdrawLoginTitle;

    /**
     * @var string
     */
    public $fiatWithdrawInviteTitle;

    /**
     * @var string
     */
    public $customInlineCss;

    /**
     * @var string
     */
    public $checkCredentialStatusUrl;

    /**
     * @var string
     */
    public $validateLoginCredentialUrl;

    /**
     * @var string
     */
    public $createPasswordUrl;

    /**
     * @var string
     */
    public $sendValidationTokenUrl;

    /**
     * @var string
     */
    public $loginUrl;

    /**
     * @var string
     */
    public $sendPasswordResetTokenUrl;

    /**
     * @var string
     */
    public $sendProvideCredentialUrl;

    /**
     * @var string
     */
    public $brandUrl;

    /**
     * @var bool
     */
    public $isGradedPercentRewardModeEnabled;

    /**
     * @var bool
     */
    public $isGoogleAnalyticsDisabled;

    /**
     * @var array
     */
    public $hiddenActions;

    /**
     * @var bool
     */
    public $isHowItWorksHidden;

    /**
     * @var string
     */
    public $setCookieUrl;

    /**
     * @var string
     */
    public $confirmShareUrl;

    /**
     * @var string
     */
    public $checkTransactionStatusUrl;

    /**
     * @var bool
     */
    public $isSignupDisabled;

    public function isAuthMethodPhone(): bool
    {
        return Partner::AUTH_METHOD_PHONE === $this->authMethod;
    }

    public function __construct()
    {
        $this->partnerSettings = new PartnerSettingsData();
    }

    public function userHasConfirmedCredentials(?User $user)
    {
        if (!$user) {
            return null;
        }

        if (Partner::AUTH_METHOD_PHONE === $this->authMethod) {
            return $user->person->credentials()
                    ->where('type_id', '=', Credential::TYPE_PHONE)
                    ->where('is_confirmed', '=', 'true')
                    ->count() > 0;
        }

        return $user->person->credentials()
                ->where('type_id', '=', Credential::TYPE_EMAIL)
                ->where('is_confirmed', '=', 'true')
                ->count() > 0;
    }
}
