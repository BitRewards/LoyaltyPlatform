<?php

namespace App\DTO\PartnerPage;

use App\DTO\Factory\PartnerSettingsFactory;
use App\Models\Action;
use App\Models\Partner;
use App\Models\User;
use App\Services\ActionProcessors\OrderReferral;
use App\Services\OAuthService;
use App\Services\PartnerService;
use Illuminate\Routing\UrlGenerator;

class PartnerDataFactory
{
    /**
     * @var PartnerService
     */
    protected $partnerService;

    /**
     * @var OAuthService
     */
    protected $oauthService;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var \HCustomizations
     */
    protected $customizationHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var PartnerSettingsFactory
     */
    protected $partnerSettingsFactory;

    public function __construct(
        PartnerService $partnerService,
        OAuthService $oauthService,
        UrlGenerator $urlGenerator,
        \HAction $actionHelper,
        \HCustomizations $customizationHelper,
        \HAmount $amountHelper,
        PartnerSettingsFactory $partnerSettingsFactory
    ) {
        $this->partnerService = $partnerService;
        $this->actionHelper = $actionHelper;
        $this->oauthService = $oauthService;
        $this->urlGenerator = $urlGenerator;
        $this->actionHelper = $actionHelper;
        $this->customizationHelper = $customizationHelper;
        $this->amountHelper = $amountHelper;
        $this->partnerSettingsFactory = $partnerSettingsFactory;
    }

    public function factory(Partner $partner, User $user = null): PartnerData
    {
        $partnerData = new PartnerData();
        $partnerData->key = $partner->key;
        $partnerData->title = $partner->title;
        $partnerData->image = $this->customizationHelper::logoPicture($partner);
        $partnerData->url = $partner->url;
        $partnerData->currencyId = $partner->currency;
        $partnerData->ethAddress = $partner->eth_address;
        $partnerData->signUpUrl = $this->partnerService->getSignupUrl($partner);
        $partnerData->authMethod = $partner->getAuthMethod();
        $partnerData->isBitRewardEnabled = $partner->isBitrewardsEnabled();
        $partnerData->isBitRewardsBrandForced = $partner->isBitRewardsBrandForced();

        $action = $partner->findActionByType(Action::TYPE_ORDER_REFERRAL);
        $partnerData->isOrderReferralActionExist = (bool) $action;

        if ($action) {
            /** @var OrderReferral $processor */
            $processor = $action->getActionProcessor();

            if (Action::VALUE_TYPE_PERCENT === $action->value_type) {
                $data = $this->actionHelper::getPercentageActionValueData($action);
            } else {
                $data = $this->actionHelper::getFixedActionValueData($action);
            }

            $partnerData->clientReferralHeading = $this->customizationHelper::clientReferralHeading($partner, $processor);
            $partnerData->clientReferralSubtitle = $this->customizationHelper::clientReferralSubtitle($partner, $data);
            $partnerData->clientReferralMinAmountNotification = $processor->getSourceEventMinAmount()
                ? $this->customizationHelper::clientReferralMinAmountNotification($partner, $processor)
                : null;
            $partnerData->referralRewardMessage = $this->actionHelper::getReferralRewardStr($processor, false);
            $partnerData->clientEventProcessorUrl = $this->generateUrl('client.events.process', $partner, ['action' => $action->id]);
        }

        $partnerData->signUpBonusAmount = $partner->getSignupBonus();
        $partnerData->fiatReferralBonus = $partner->getFiatReferralBonusStr();
        $partnerData->signUpBonus = $this->amountHelper::points($partnerData->signUpBonusAmount);

        $partnerData->oauthFBUrl = $this->oauthService->url($this->oauthService::FB_SOCIAL_NETWORK, $partner);
        $partnerData->oauthVKUrl = $this->oauthService->url($this->oauthService::VK_SOCIAL_NETWORK, $partner);
        $partnerData->oauthGoogleUrl = $this->oauthService->url($this->oauthService::GOOGLE_SOCIAL_NETWORK, $partner);
        $partnerData->twitterAuthUrl = $this->generateUrl('client.twitterOAuth', $partner);
        $partnerData->clientSupportUrl = $this->generateUrl('client.support', $partner);
        $partnerData->clientEventAcquireCodeUrl = $this->generateUrl('client.events.acquireCode', $partner);
        $partnerData->clientRewardBitRewardsPayoutUrl = $this->generateUrl('client.reward.bitrewardsPayout', $partner);
        $partnerData->clientRewardConfirmDepositUrl = $this->generateUrl('client.reward.confirmDeposit', $partner);
        $partnerData->clientConfirmPhoneUrl = $this->generateUrl('client.confirmPhone', $partner);
        $partnerData->clientProvidePhoneUrl = $this->generateUrl('client.providePhone', $partner);
        $partnerData->resetPasswordByRequestUrl = $this->generateUrl('client.resetByPhoneRequest', $partner);
        $partnerData->clientResetPasswordUrl = $this->generateUrl('client.reset', $partner);
        $partnerData->updateWalletAddressUrl = $this->generateUrl('client.reward.updateWalletAddress', $partner);
        $partnerData->mergeByEmailUrl = $this->generateUrl('client.mergeByEmail', $partner);
        $partnerData->mergeByPhoneUrl = $this->generateUrl('client.mergeByPhone', $partner);
        $partnerData->checkEmailIsConfirmedUrl = $this->generateUrl('client.checkEmailIsConfirmed', $partner);
        $partnerData->inviteUrl = $this->generateUrl('client.invite', $partner);
        $partnerData->checkEmailUrl = $this->generateUrl('client.checkEmail', $partner);
        $partnerData->checkPhoneUrl = $this->generateUrl('client.checkPhone', $partner);
        $partnerData->forgotUrl = $this->generateUrl('client.forgot', $partner);
        $partnerData->indexPageUrl = $this->generateUrl('client.index', $partner);
        $partnerData->supportUrl = $this->generateUrl('client.support', $partner);
        $partnerData->getConfirmationStatusUrl = $this->generateUrl('client.getConfirmationStatus', $partner);
        $partnerData->sendPhoneConfirmationUrl = $this->generateUrl('client.sendPhoneConfirmation', $partner);
        $partnerData->sendMergeByPhoneConfirmationUrl = $this->generateUrl('client.sendMergeByPhoneConfirmation', $partner);
        $partnerData->sendMergeByEmailConfirmationUrl = $this->generateUrl('client.sendMergeByEmailConfirmation', $partner);
        $partnerData->addEmailUrl = $this->generateUrl('clients.person.addEmail', $partner);
        $partnerData->confirmEmailUrl = $this->generateUrl('clients.person.confirmEmail', $partner);
        $partnerData->addPhoneUrl = $this->generateUrl('clients.person.addPhone', $partner);
        $partnerData->confirmPhoneUrl = $this->generateUrl('clients.person.confirmPhone', $partner);
        $partnerData->fiatWithdrawUrl = $this->generateUrl('client.reward.fiatWithdraw', $partner);
        $partnerData->setCookieUrl = $this->generateUrl('client.setAppCookies', $partner);
        $partnerData->primaryColor = $this->customizationHelper::primaryColor($partner);
        $partnerData->isMenuFontBolder = $partner->getSetting(Partner::SETTINGS_IS_MENU_FONT_BOLDER) ?: false;
        $partnerData->partnerSettings = $this->partnerSettingsFactory->factory($partner);
        $partnerData->brandUrl = $partner->getSetting(Partner::SETTINGS_BRAND_URL);
        $partnerData->confirmShareUrl = $this->generateUrl('client.action.confirmShare', $partner);
        $partnerData->checkTransactionStatusUrl = $this->generateUrl('client.action.confirmShare.checkTransactionStatus', $partner);

        $partnerData->checkCredentialStatusUrl =
            Partner::AUTH_METHOD_PHONE === $partner->getAuthMethod()
                ? $this->generateUrl('client.authentication.checkPhoneStatus', $partner)
                : $this->generateUrl('client.authentication.checkEmailStatus', $partner);
        $partnerData->validateLoginCredentialUrl =
            Partner::AUTH_METHOD_PHONE === $partner->getAuthMethod()
                ? $this->generateUrl('client.authentication.validatePhone', $partner)
                : $this->generateUrl('client.authentication.validateEmail', $partner);
        $partnerData->createPasswordUrl = $this->generateUrl('client.authentication.setPassword', $partner);
        $partnerData->sendValidationTokenUrl =
            Partner::AUTH_METHOD_PHONE === $partner->getAuthMethod()
                ? $this->generateUrl('client.authentication.sendPhoneValidationToken', $partner)
                : $this->generateUrl('client.authentication.sendEmailValidationToken', $partner);
        $partnerData->loginUrl = $this->generateUrl('client.authentication.login', $partner);
        $partnerData->sendPasswordResetTokenUrl = $this->generateUrl('client.authentication.sendPasswordResetToken', $partner);
        $partnerData->sendProvideCredentialUrl =
            Partner::AUTH_METHOD_PHONE === $partner->getAuthMethod()
                ? $this->generateUrl('client.authentication.providePhone', $partner)
                : $this->generateUrl('client.authentication.provideEmail', $partner);

        /* @todo move partner settings to partnerSettingsData */
        $partnerData->brwAmount = 0;
        $partnerData->brwToUsdRate = 0.03;
        $partnerData->withdrawFeeType = $partner->getBitWithdrawFeeType();
        $partnerData->withdrawFeeValue = $partner->getBitWithdrawFee();
        $partnerData->withdrawAmountMin = $partner->getBitWithdrawMinAmount();
        $partnerData->withdrawAmountMax = $user ? $user->balance : $partner->getBitWithdrawMinAmount() * 2;
        $partnerData->isFiatReferrerEnabled = $partner->isFiatReferralEnabled();
        $partnerData->isSignupDisabled = $partner->isSignupDisabled();
        $partnerData->isReferralLinkEnabled = $partner->isReferralLinkEnabled();
        $partnerData->isReferralPromoCodeEnabled = $partner->isReferralPromoCodeEnabled();
        $partnerData->referrerStatisticUrl = $this->generateUrl('client.user.referralStatistic', $partner);
        $partnerData->isOborotPromoPartner = $partner->isOborotPromoPartner();
        $partnerData->isBazelevsPartner = $partner->isBazelevsPartner();
        $partnerData->isAvtocodPartner = $partner->isAvtocodPartner();
        $partnerData->isFightwearPromoPartner = $partner->isFightwearPromoPartner();
        $partnerData->isMigoff = $partner->isMigoffPartner();
        $partnerData->fiatWithdrawLoginTitle = $partner->getFiatWithdrawLoginTitle();
        $partnerData->fiatWithdrawInviteTitle = $partner->getFiatWithdrawInviteTitle(__('Referral link'));
        $partnerData->customInlineCss = $partner->getCustomInlineCss();
        $partnerData->isMergeBalancesEnabled = $partner->isMergeBalancesEnabled();
        $partnerData->isHowItWorksHidden = $partner->getSetting(Partner::SETTINGS_IS_HOW_IT_WORKS_HIDDEN);
        $partnerData->isAuthViaSocialNetworksHidden = $partner->getSetting(Partner::SETTINGS_AUTH_VIA_SOCIAL_NETWORKS_HIDDEN);
        $partnerData->isGradedPercentRewardModeEnabled = $partner->isGradedPercentRewardModeEnabled();
        $partnerData->isGoogleAnalyticsDisabled = $partner->isGoogleAnalyticsDisabled();
        $partnerData->hiddenActions = $partner->getHiddenActions();

        return $partnerData;
    }

    protected function generateUrl(string $routeName, Partner $partner, array $params = []): string
    {
        return $this
            ->urlGenerator
            ->route($routeName, [
                'partner' => $partner->key,
            ] + $params);
    }
}
