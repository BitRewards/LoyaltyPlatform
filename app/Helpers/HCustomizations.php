<?php

use App\Models\Partner;
use App\DTO\ActionValueData;
use App\Services\CustomizationsService;
use App\Services\ActionProcessors\OrderReferral;
use App\Settings\PartnerSettings;

class HCustomizations
{
    /**
     * @param Partner $partner
     * @param string  $name
     * @param $default = null
     *
     * @return string|int|null
     */
    public static function setting(Partner $partner, string $name, $default = null)
    {
        return $partner->settings()->get($name) ?: $default;
    }

    /**
     * @param Partner $partner
     * @param $default = null
     *
     * @return string|null
     */
    public static function primaryColor(Partner $partner, $default = null)
    {
        return static::setting($partner, PartnerSettings::PRIMARY_COLOR, $default);
    }

    /**
     * @param Partner $partner
     * @param $overridenTitle = null
     *
     * @return string|null
     */
    public static function clientAppTitle(Partner $partner, $overridenTitle = null)
    {
        return app(CustomizationsService::class)->formatAppTitle(
            $partner,
            static::setting($partner, PartnerSettings::CLIENT_APP_TITLE),
            $overridenTitle
        );
    }

    /**
     * @param Partner $partner
     *
     * @return bool
     */
    public static function activatePlasticBeforeOtherActions(Partner $partner): bool
    {
        return 1 === intval(static::setting($partner, PartnerSettings::ACTIVATE_CARD_FIRST));
    }

    /**
     * @param Partner $partner
     *
     * @return bool
     */
    public static function discountInsteadOfLoyalty(Partner $partner)
    {
        return 1 === intval(static::setting($partner, PartnerSettings::USE_DISCOUNT_CARD));
    }

    /**
     * @param Partner $partner
     * @param $default = null
     *
     * @return string|null
     */
    public static function logoPicture(Partner $partner, $default = null)
    {
        return static::setting($partner, 'logo_picture', $default);
    }

    /**
     * @param Partner $partner
     *
     * @return string|null
     */
    public static function balanceChangedEmailHeading(Partner $partner)
    {
        if ($partner->isFiatReferralEnabled()) {
            return app(CustomizationsService::class)->formatBalanceChangedEmailHeadingTextFiat(
                $partner,
                static::setting($partner, PartnerSettings::BALANCE_CHANGED_HEADING_FIAT)
            );
        }

        return app(CustomizationsService::class)->formatBalanceChangedEmailHeadingText(
            $partner,
            static::setting($partner, PartnerSettings::BALANCE_CHANGED_HEADING)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function referralEmailBlockContent(Partner $partner)
    {
        return app(CustomizationsService::class)->formatReferralRewardContent(
            $partner,
            static::setting($partner, PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function referralEmailBlockFirstStep(Partner $partner)
    {
        return app(CustomizationsService::class)->formatReferralRewardIntro(
            $partner,
            static::setting($partner, PartnerSettings::REFERRAL_EMAIL_BLOCK_FIRST_STEP)
        );
    }

    /**
     * @param Partner         $partner
     * @param ActionValueData $data
     *
     * @return string
     */
    public static function referralEmailBlockSecondStep(Partner $partner, ActionValueData $data)
    {
        return app(CustomizationsService::class)->formatReferralRewardText(
            $partner,
            $data,
            static::setting($partner, PartnerSettings::REFERRAL_EMAIL_BLOCK_SECOND_STEP)
        );
    }

    /**
     * @param Partner       $partner
     * @param OrderReferral $processor
     *
     * @return string
     */
    public static function referralEmailBlockThirdStep(Partner $partner, OrderReferral $processor)
    {
        return app(CustomizationsService::class)->formatReferredFriendRewardText(
            $partner,
            $processor,
            static::setting($partner, PartnerSettings::REFERRAL_EMAIL_BLOCK_THIRD_STEP)
        );
    }

    /**
     * @param Partner       $partner
     * @param OrderReferral $processor
     *
     * @return string
     */
    public static function clientReferralHeading(Partner $partner, OrderReferral $processor)
    {
        return app(CustomizationsService::class)->formatClientReferralHeadingText(
            $partner,
            $processor,
            static::setting($partner, PartnerSettings::REFERRAL_CLIENT_BLOCK_HEADING)
        );
    }

    /**
     * @param Partner         $partner
     * @param ActionValueData $data
     *
     * @return string
     */
    public static function clientReferralSubtitle(Partner $partner, ActionValueData $data)
    {
        return app(CustomizationsService::class)->formatClientReferralSubtitleText(
            $partner,
            $data,
            static::setting($partner, PartnerSettings::REFERRAL_CLIENT_BLOCK_SUBTITLE)
        );
    }

    /**
     * @param Partner       $partner
     * @param OrderReferral $processor
     *
     * @return string
     */
    public static function clientReferralMinAmountNotification(Partner $partner, OrderReferral $processor)
    {
        return app(CustomizationsService::class)->formatClientReferralMinAmountText(
            $partner,
            $processor,
            static::setting($partner, PartnerSettings::REFERRAL_CLIENT_BLOCK_MIN_AMOUNT)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function howItWorksFirstStepTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatHowItWorksStepTipMessage(
            $partner,
            PartnerSettings::HOW_IT_WORKS_FIRST_STEP_TIP_MESSAGE
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function howItWorksSecondStepTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatHowItWorksStepTipMessage(
            $partner,
            PartnerSettings::HOW_IT_WORKS_SECOND_STEP_TIP_MESSAGE
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function howItWorksThirdStepTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatHowItWorksStepTipMessage(
            $partner,
            PartnerSettings::HOW_IT_WORKS_THIRD_STEP_TIP_MESSAGE
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function dashboardOnboardLabel(Partner $partner)
    {
        return app(CustomizationsService::class)->formatDashboardOnboardLabel(
            $partner,
            static::setting($partner, PartnerSettings::DASHBOARD_ONBOARDING_LABEL)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function earnInviteAndEarnTitle(Partner $partner)
    {
        return app(CustomizationsService::class)->formatEarnInviteAndEarnTitle(
            $partner,
            static::setting($partner, PartnerSettings::EARN_INVITE_AND_EARN_TITLE)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function dashboardTotalCashbackAmountLabel(Partner $partner)
    {
        return app(CustomizationsService::class)->formatDashboardTotalCashbackAmountLabel(
            $partner,
            static::setting($partner, PartnerSettings::DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function referrerBalanceTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatReferrerBalanceTipMessage(
            $partner,
            static::setting($partner, PartnerSettings::REFERRER_BALANCE_TIP_MESSAGE)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function referrerBalanceAvailableFundsTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatReferrerBalanceAvailableFundsTipMessage(
            $partner,
            static::setting($partner, PartnerSettings::REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function referrerBalanceTotalEarnedTipMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatReferrerTotalEarnedTipMessage(
            $partner,
            static::setting($partner, PartnerSettings::REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function termsOfServiceLink(Partner $partner)
    {
        return app(CustomizationsService::class)->formatTermsOfServiceLink(
            $partner,
            static::setting($partner, PartnerSettings::TERMS_OF_SERVICE_LINK)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function onAuthOpenedTabId(Partner $partner)
    {
        return app(CustomizationsService::class)->formatOnAuthOpenedTabId(
            $partner,
            static::setting($partner, PartnerSettings::ON_AUTH_OPENED_TAB_ID)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function instagramActionSendUsPostMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatInstagramActionSendUsThePostMessage(
            $partner,
            static::setting($partner, PartnerSettings::INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE)
        );
    }

    /**
     * @param Partner $partner
     *
     * @return string
     */
    public static function instagramActionDescriptionMessage(Partner $partner)
    {
        return app(CustomizationsService::class)->formatInstagramActionDescriptionMessage(
            $partner,
            static::setting($partner, PartnerSettings::INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE)
        );
    }
}
