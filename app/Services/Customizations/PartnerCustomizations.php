<?php

namespace App\Services\Customizations;

use App\Models\Partner;
use App\Settings\PartnerSettings;

class PartnerCustomizations
{
    public function __construct()
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function buildCustomizations()
    {
        return collect([
            [
                'name' => __('Primary Color'),
                'setting' => PartnerSettings::PRIMARY_COLOR,
                'default' => '#FDA63B',
            ],
            [
                'name' => __('Client Application Title'),
                'setting' => PartnerSettings::CLIENT_APP_TITLE,
                'default' => __('{partnerTitle} Rewards Program'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('Display card activation before all actions?'),
                'setting' => PartnerSettings::ACTIVATE_CARD_FIRST,
                'default' => '0',
                'type' => 'select',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No'),
                ],
            ],
            [
                'name' => __('Use discount card instead of loyalty card?'),
                'setting' => PartnerSettings::USE_DISCOUNT_CARD,
                'default' => '0',
                'type' => 'select',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No'),
                ],
            ],
            [
                'name' => __('Logo URL'),
                'setting' => PartnerSettings::LOGO_PICTURE,
                'default' => '',
            ],
            [
                'name' => __("Balance Changed email's heading"),
                'setting' => PartnerSettings::BALANCE_CHANGED_HEADING,
                'default' => __('You are a member of «{partnerTitle}» Rewards Program'),
                'placeholder' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __("Balance Changed email's heading"),
                'setting' => PartnerSettings::BALANCE_CHANGED_HEADING_FIAT,
                'default' => __('You are a member of «{partnerTitle}» Referral Program'),
                'placeholder' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('Referral Section heading in Balance Changed email'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT,
                'default' => __('Share this link with your friends, get more rewards!'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('Referral Section heading in Balance Changed email for referral promo code enabled'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT_REFERRAL_PROMO_CODE,
                'default' => __('Share this code with your friends, get more rewards!'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('Referral Section heading in Balance Changed email'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_CONTENT_FIAT,
                'default' => __('Share this link with your friends, get more money!'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('First step of Referral Section of Balance Changed email'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_FIRST_STEP,
                'default' => __('Send this link to your friends!'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('First step of Referral Section of Balance Changed email for referral promo code enabled'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_FIRST_STEP_REFERRAL_PROMO_CODE,
                'default' => __('Send this code to your friends!'),
                'placeholders' => $this->defaultPlaceholders(),
                'preview_replacements' => $this->previewReplacements(),
            ],
            [
                'name' => __('Second step of Referral Section of Balance Changed email'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_SECOND_STEP,
                'default' => __('You will receive {pointsStr} for every {amountStr} of purchases by your friends.'),
                'placeholders' => $this->defaultPlaceholders([
                    'pointsStr' => __('Points'),
                    'amountStr' => __('Purchase Amount'),
                ]),
                'preview_replacements' => $this->previewReplacements([
                    'pointsStr' => __('10 points'),
                    'amountStr' => __('$5'),
                ]),
            ],
            [
                'name' => __('First step of Referral Section of Balance Changed email'),
                'setting' => PartnerSettings::REFERRAL_EMAIL_BLOCK_THIRD_STEP,
                'default' => __('Each of your friends will receive a discount {discountStr}.'),
                'placeholders' => $this->defaultPlaceholders([
                    'discountStr' => __('Discount value'),
                ]),
                'preview_replacements' => $this->previewReplacements([
                    'discountStr' => __('10%'),
                ]),
            ],
            [
                'name' => __('Invite a Friend\' section heading of Client Application'),
                'setting' => PartnerSettings::REFERRAL_CLIENT_BLOCK_HEADING,
                'default' => __('Your friends will receive a {discountStr} discount'),
                'placeholders' => $this->defaultPlaceholders([
                    'discountStr' => __('Discount value (HTML included)'),
                    'discount' => __('Discount value (no HTML included)'),
                ]),
                'preview_replacements' => $this->previewReplacements([
                    'discount' => __('10%'),
                    'discountStr' => __('10%'),
                ]),
            ],
            [
                'name' => __("Invite a Friend' section subtitle in Client Application"),
                'setting' => PartnerSettings::REFERRAL_CLIENT_BLOCK_SUBTITLE,
                'default' => __('You will receive {pointsStr} for every {amountStr} of purchases by your friends.'),
                'placeholders' => $this->defaultPlaceholders([
                    'pointsStr' => __('Points amount (HTML included)'),
                    'points' => __('Points amount (no HTML included)'),
                    'amountStr' => __('Purchase amount (HTML included)'),
                    'amount' => __('Purchase amount (no HTML included)'),
                ]),
                'preview_replacements' => $this->previewReplacements([
                    'pointsStr' => __('50 points'),
                    'points' => __('50 points'),
                    'amountStr' => __('$5'),
                    'amount' => __('$5'),
                ]),
            ],
            [
                'name' => __('Minimal purchase amount notification in Invite a Friend\'s section of Client Application'),
                'setting' => PartnerSettings::REFERRAL_CLIENT_BLOCK_MIN_AMOUNT,
                'default' => __('if they spend more than {amountStr}'),
                'placeholders' => $this->defaultPlaceholders([
                    'amountStr' => __('Purchase amount (HTML included)'),
                    'amount' => __('Purchase amount (no HTML included)'),
                ]),
                'preview_replacements' => $this->previewReplacements([
                    'amountStr' => __('$20'),
                    'amount' => __('$20'),
                ]),
            ],
            [
                'name' => _('First step in how it works modal'),
                'setting' => PartnerSettings::HOW_IT_WORKS_FIRST_STEP_TIP_MESSAGE,
                'default' => __('Copy the link to any product on the website by clicking the button at the bottom of each product’s page.'),
            ],
            [
                'name' => _('Second step in how it works modal'),
                'setting' => PartnerSettings::HOW_IT_WORKS_SECOND_STEP_TIP_MESSAGE,
                'default' => __('Share the copied link and get cashback for your referrals’ purchases.'),
            ],
            [
                'name' => _('Third step in how it works modal'),
                'setting' => PartnerSettings::HOW_IT_WORKS_THIRD_STEP_TIP_MESSAGE,
                'default' => __('Go to &laquo;Your balance&raquo;, click on the &laquo;Withdraw funds&raquo; button, enter your full name, card number and withdrawal amount and receive earnings on your Visa or MasterCard bank card.'),
            ],
            [
                'name' => _('Onboarding label on dashboard page'),
                'setting' => PartnerSettings::DASHBOARD_ONBOARDING_LABEL,
                'default' => __("Start sharing links to&nbsp;products' pages to&nbsp;get more results!"),
            ],
            [
                'name' => _('Referral link heading'),
                'setting' => PartnerSettings::EARN_INVITE_AND_EARN_TITLE,
                'default' => __('Share the link to&nbsp;this store and earn:'),
            ],
            [
                'name' => _('Total cashback amount label'),
                'setting' => PartnerSettings::DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL,
                'default' => __('Total cashback amount'),
            ],
            [
                'name' => _('Referrer balance tip message'),
                'setting' => PartnerSettings::REFERRER_BALANCE_TIP_MESSAGE,
                'default' => __('You can view the current balance, transfer the earned money to Visa and MasterCard cards and use the saved coupons in the Balance section.'),
            ],
            [
                'name' => _('Referrer balance available funds tip message'),
                'setting' => PartnerSettings::REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE,
                'default' => __('&laquo;Available for withdrawal&raquo;&nbsp;&mdash; is&nbsp;the amount that you can withdraw to&nbsp;a&nbsp;bank card. It&nbsp;may differ from the &laquo;Current Balance&raquo; because it&nbsp;takes up&nbsp;to&nbsp;28 days to&nbsp;receive a&nbsp;cashback.'),
            ],
            [
                'name' => _('Referrer balance total earned tip message'),
                'setting' => PartnerSettings::REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE,
                'default' => _('&laquo;Total Earned&raquo;&nbsp;&mdash; is&nbsp;the amount of&nbsp;money earned for the entire period. This is&nbsp;the sum of&nbsp;all cashbacks that you received for referral purchases for the entire period of&nbsp;work.'),
            ],
            [
                'name' => _('TOS Link'),
                'setting' => PartnerSettings::TERMS_OF_SERVICE_LINK,
                'default' => '/loyalty/docs/BitRewads_Referral_Tool.Offer_for_agents.pdf',
            ],
            [
                'name' => _('Which tab should be opened on auth'),
                'setting' => PartnerSettings::ON_AUTH_OPENED_TAB_ID,
                'default' => '',
                'type' => 'select',
                'options' => [
                    '' => __('Default'),
                    'spend' => __('Spend'),
                    'earn' => __('Earn'),
                    'invite' => __('Invite friends'),
                    'history' => __('History'),
                    'balance' => __('Balance'),
                    'profile' => __('Profile'),
                ],
            ],
            [
                'name' => _('Instagram action - send us post message'),
                'setting' => PartnerSettings::INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE,
                'default' => __('Send us the post'),
            ],
            [
                'name' => _('Instagram action - description message'),
                'setting' => PartnerSettings::INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE,
                'default' => __('Artificial Intelligence will check the content of the image and give you a reward.'),
            ],
        ]);
    }

    /**
     * @param array $settings
     *
     * @return array
     */
    public function validateSettings(array $settings)
    {
        $castToPseudoBoolean = [
            PartnerSettings::ACTIVATE_CARD_FIRST,
            PartnerSettings::USE_DISCOUNT_CARD,
        ];

        foreach ($castToPseudoBoolean as $setting) {
            if (isset($settings[$setting])) {
                $settings[$setting] = 1 === intval($settings[$setting]) ? 1 : 0;
            }
        }

        return $settings;
    }

    /**
     * @param Partner $partner
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(Partner $partner)
    {
        return $this->buildCustomizations()->map(function (array $customization) use ($partner) {
            $customization['value'] = $partner->settings($customization['setting']);

            return $customization;
        });
    }

    /**
     * @param string $setting
     *
     * @return mixed
     */
    public function defaultValueFor(string $setting)
    {
        $index = $this->buildCustomizations()->search(function (array $customization) use ($setting) {
            return $customization['setting'] === $setting;
        });

        if (false === $index) {
            throw new \InvalidArgumentException('Given setting ('.$setting.') was not found in partner customizations list.');
        }

        $customization = $this->buildCustomizations()->get($index);

        if (!isset($customization['default'])) {
            throw new \InvalidArgumentException('Given setting ('.$setting.') has no default value.');
        }

        return $customization['default'];
    }

    /**
     * @param array $customPlaceholders
     *
     * @return array
     */
    public function defaultPlaceholders(array $customPlaceholders = [])
    {
        return array_merge([
            'partnerTitle' => __('Partner Name'),
        ], $customPlaceholders);
    }

    /**
     * @param array $customReplacements
     *
     * @return array
     */
    public function previewReplacements(array $customReplacements = [])
    {
        return array_merge([
            'partnerTitle' => 'Umbrella Corp.',
        ], $customReplacements);
    }
}
