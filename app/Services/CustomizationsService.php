<?php

namespace App\Services;

use App\DTO\ActionValueData;
use App\Models\Partner;
use App\Services\ActionProcessors\OrderReferral;
use App\Services\Customizations\PartnerCustomizations;
use App\Settings\PartnerSettings;
use LaravelPropertyBag\Exceptions\InvalidSettingsValue;

class CustomizationsService
{
    protected $partnerCustomizations;

    /**
     * CustomizationsService constructor.
     *
     * @param PartnerCustomizations $partnerCustomizations
     */
    public function __construct(PartnerCustomizations $partnerCustomizations)
    {
        $this->partnerCustomizations = $partnerCustomizations;
    }

    /**
     * Updates partner settings.
     *
     * @param Partner $partner
     * @param array   $settings
     *
     * @return array
     */
    public function updateCustomizationSettings(Partner $partner, array $settings)
    {
        $settings = $this->partnerCustomizations->validateSettings($settings);

        try {
            $partner->settings()->set($settings);
        } catch (InvalidSettingsValue $e) {
            return ['error' => 'Field '.$e->getFailedKey().' has incorrect value'];
        }

        return ['data' => 'ok'];
    }

    /**
     * Formats Client App's title.
     *
     * @param Partner     $partner
     * @param string|null $text
     * @param string|null $overridenTitle
     *
     * @return string
     */
    public function formatAppTitle(Partner $partner, string $text = null, string $overridenTitle = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::CLIENT_APP_TITLE);
        $partnerTitle = $overridenTitle ?? $partner->title;

        return $this->formatCustomPartnerText($partner, $text, [
            'partnerTitle' => '<span class="i__text">'.$partnerTitle.'</span>',
        ]);
    }

    /**
     * Formats balance-changed email's heading text.
     *
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatBalanceChangedEmailHeadingText(Partner $partner, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::BALANCE_CHANGED_HEADING);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * Formats balance-changed email's heading text.
     *
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatBalanceChangedEmailHeadingTextFiat(Partner $partner, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::BALANCE_CHANGED_HEADING_FIAT);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * Formats balance-changed email's intro text.
     *
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatReferralRewardContent(Partner $partner, string $text = null)
    {
        $setting = $partner->isFiatReferralEnabled() ? PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT_FIAT : PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT;

        if ($partner->isReferralPromoCodeEnabled()) {
            $setting = PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT_REFERRAL_PROMO_CODE;
        }

        $text = $text ?? $this->partnerCustomizations->defaultValueFor($setting);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * Formats first step in balance-changed email's referral section.
     *
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatReferralRewardIntro(Partner $partner, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor($partner->isReferralPromoCodeEnabled() ? PartnerSettings::REFERRAL_EMAIL_BLOCK_FIRST_STEP_REFERRAL_PROMO_CODE : PartnerSettings::REFERRAL_EMAIL_BLOCK_FIRST_STEP);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * Formats second step in balance-changed email's referral section.
     *
     * @param Partner         $partner
     * @param ActionValueData $data
     * @param string|null     $text
     *
     * @return string
     */
    public function formatReferralRewardText(Partner $partner, ActionValueData $data, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRAL_EMAIL_BLOCK_SECOND_STEP);

        if ($partner->isFiatReferralEnabled()) {
            $pointsStr = \HAmount::fShort(\HAmount::pointsToFiat($data->points, $partner), $partner->currency);
        } else {
            $pointsStr = \HAmount::points($data->points, $partner);
        }

        return $this->formatCustomPartnerText($partner, $text, [
            'points' => $data->points,
            'pointsStr' => $pointsStr,
            'amountStr' => \HAmount::fShort($data->amount, $partner->currency),
        ]);
    }

    /**
     * Formats third step in balance-changed email's referral section.
     *
     * @param Partner       $partner
     * @param OrderReferral $processor
     * @param string|null   $text
     *
     * @return string
     */
    public function formatReferredFriendRewardText(Partner $partner, OrderReferral $processor, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRAL_EMAIL_BLOCK_THIRD_STEP);

        return $this->formatCustomPartnerText($partner, $text, [
            'discountStr' => \HAction::getValueStr(
                $partner,
                $processor->getReferralRewardValue(),
                $processor->getReferralRewardValueType()
            ),
        ]);
    }

    /**
     * Formats Client App's referral heading text.
     *
     * @param Partner       $partner
     * @param OrderReferral $processor
     * @param string|null   $text
     *
     * @return string
     */
    public function formatClientReferralHeadingText(Partner $partner, OrderReferral $processor, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRAL_CLIENT_BLOCK_HEADING);
        $discount = \HAction::getReferralRewardStr($processor);

        return $this->formatCustomPartnerText($partner, $text, [
            'discount' => $discount,
            'discountStr' => '<span class="i__text is-bold">'.$discount.'</span>',
        ]);
    }

    /**
     * Formats Client App's referral subtitle text.
     *
     * @param Partner         $partner
     * @param ActionValueData $data
     * @param string|null     $text
     *
     * @return string
     */
    public function formatClientReferralSubtitleText(Partner $partner, ActionValueData $data, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRAL_CLIENT_BLOCK_SUBTITLE);

        if ($partner->isFiatReferralEnabled()) {
            $points = \HAmount::fSignBold(\HAmount::pointsToFiat($data->points, $partner), $partner->currency);
        } else {
            $points = \HAmount::points($data->points);
        }

        $amount = \HAmount::fSignBold($data->amount, $partner->currency);

        return $this->formatCustomPartnerText($partner, $text, [
            'points' => $points,
            'amount' => $amount,
            'pointsStr' => '<nobr><span class="i__text">'.$points.'</span></nobr>',
            'amountStr' => '<nobr><span class="i__text">'.$amount.'</span></nobr>',
        ]);
    }

    /**
     * Formats Client App's minimal purchase amount text.
     *
     * @param Partner       $partner
     * @param OrderReferral $processor
     * @param string|null   $text
     *
     * @return string
     */
    public function formatClientReferralMinAmountText(Partner $partner, OrderReferral $processor, string $text = null)
    {
        $text = $text ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRAL_CLIENT_BLOCK_MIN_AMOUNT);
        $amount = \HAmount::fSign($processor->getSourceEventMinAmount(), $partner->currency);

        return $this->formatCustomPartnerText($partner, $text, [
            'amount' => $amount,
            'amountStr' => '<span class="i__text is-bold">'.$amount.'</span>',
        ]);
    }

    /**
     * Formats partner text with predefined replacements & given custom replacements.
     *
     * @param Partner $partner
     * @param string  $text
     * @param array   $customReplacements = []
     *
     * @return string
     */
    public function formatCustomPartnerText(Partner $partner, string $text, array $customReplacements = [])
    {
        return $this->replacePlaceholders($text, array_merge([
            'partnerTitle' => $partner->title,
        ], $customReplacements));
    }

    /**
     * Performs placeholders replacement.
     *
     * @param string $text
     * @param array  $rules = []
     *
     * @return string
     */
    public function replacePlaceholders(string $text, array $rules = [])
    {
        $search = array_map(function ($item) {
            if ($item === '{'.$item.'}') {
                return $item;
            }

            return '{'.$item.'}';
        }, array_keys($rules));
        $replace = array_values($rules);

        return str_replace($search, $replace, $text);
    }

    /**
     * @param Partner $partner
     */
    public function migrateCustomizations(Partner $partner)
    {
        $customizations = $this->getLegacyCustomizationsMap();
        $settings = $partner->settings();
        $partnerSettings = collect([]);

        // We have customizations map (old => new) that we can iterate through,
        // and set any value to the overriden one (legacy). After this we'll
        // update partner settings, so they can use new customizations.

        collect($customizations)->each(function (array $setting, string $original) use ($partner, $settings, $partnerSettings) {
            $legacyValue = $partner->getCustomization($original);

            // NULL means that partner is using default setting value.

            if (is_null($legacyValue)) {
                return true;
            }

            // Boolean settings values are stored as integer 0 or 1.
            // Let's cast any value to 1 or 0 if this is required.

            if (true === array_get($setting, 'cast_boolean')) {
                $legacyValue = 1 === intval($legacyValue) ? 1 : 0;
            }

            // If current legacy value is equals to default setting's value
            // we don't need to update this setting as well, just ignore.

            if ($settings->isDefault($setting['name'], $legacyValue)) {
                return true;
            }

            $partnerSettings->put($setting['name'], $legacyValue);
        });

        if ($partnerSettings->count() > 0) {
            $settings->set($partnerSettings->toArray());
        }
    }

    public function customizationsFor(Partner $partner)
    {
        return $this->partnerCustomizations->all($partner);
    }

    /**
     * @return array
     */
    protected function getLegacyCustomizationsMap(): array
    {
        return [
            Partner::CUSTOMIZATION_PRIMARY_COLOR => [
                'name' => 'primary_color',
            ],
            Partner::CUSTOMIZATION_CLIENT_APP_TITLE => [
                'name' => 'client_app_title',
            ],
            Partner::CUSTOMIZATION_ACTIVATE_PLASTIC_CARD_BEFORE_OTHER_ACTIONS => [
                'name' => 'activate_plastic_card_before_other_actions',
                'cast_boolean' => true,
            ],
            Partner::CUSTOMIZATION_DISCOUNT_CARD_INSTEAD_OF_LOYALTY_CARD => [
                'name' => 'discount_card_instead_of_loyalty_card',
                'cast_boolean' => true,
            ],
            Partner::CUSTOMIZATION_LOGO_PICTURE => [
                'name' => 'logo_picture',
            ],
            Partner::CUSTOMIZATION_EMAIL_BALANCE_CHANGED_FIRST_LINE => [
                'name' => 'balance_changed_email_heading',
            ],
        ];
    }

    /**
     * @param Partner $partner
     * @param $step
     *
     * @return string
     */
    public function formatHowItWorksStepTipMessage(Partner $partner, $step)
    {
        $text = \HCustomizations::setting($partner, $step) ?? $this->partnerCustomizations->defaultValueFor($step);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatDashboardOnboardLabel(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::DASHBOARD_ONBOARDING_LABEL) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::DASHBOARD_ONBOARDING_LABEL);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatEarnInviteAndEarnTitle(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::EARN_INVITE_AND_EARN_TITLE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::EARN_INVITE_AND_EARN_TITLE);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatDashboardTotalCashbackAmountLabel(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatReferrerBalanceTipMessage(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::REFERRER_BALANCE_TIP_MESSAGE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRER_BALANCE_TIP_MESSAGE);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatReferrerBalanceAvailableFundsTipMessage(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatReferrerTotalEarnedTipMessage(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatTermsOfServiceLink(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::TERMS_OF_SERVICE_LINK) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::TERMS_OF_SERVICE_LINK);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatOnAuthOpenedTabId(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::ON_AUTH_OPENED_TAB_ID) ?? '';

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatInstagramActionSendUsThePostMessage(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE);

        return $this->formatCustomPartnerText($partner, $text);
    }

    /**
     * @param Partner     $partner
     * @param string|null $text
     *
     * @return string
     */
    public function formatInstagramActionDescriptionMessage(Partner $partner, string $text = null)
    {
        $text = \HCustomizations::setting($partner, PartnerSettings::INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE) ?? $this->partnerCustomizations->defaultValueFor(PartnerSettings::INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE);

        return $this->formatCustomPartnerText($partner, $text);
    }
}
