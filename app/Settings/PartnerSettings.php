<?php

namespace App\Settings;

use LaravelPropertyBag\Settings\ResourceConfig;

class PartnerSettings extends ResourceConfig
{
    const PRIMARY_COLOR = 'primary_color';
    const CLIENT_APP_TITLE = 'client_app_title';
    const ACTIVATE_CARD_FIRST = 'activate_plastic_card_before_other_actions';
    const USE_DISCOUNT_CARD = 'discount_card_instead_of_loyalty_card';
    const LOGO_PICTURE = 'logo_picture';
    const BALANCE_CHANGED_HEADING = 'balance_changed_email_heading';
    const BALANCE_CHANGED_HEADING_FIAT = 'balance_changed_email_heading_fiat';
    const REFERRAL_EMAIL_BLOCK_CONTENT = 'referral_email_block_content_text';
    const REFERRAL_EMAIL_BLOCK_CONTENT_REFERRAL_PROMO_CODE = 'referral_email_block_content_text_referral_code';
    const REFERRAL_EMAIL_BLOCK_CONTENT_FIAT = 'referral_email_block_content_text_fiat';
    const REFERRAL_EMAIL_BLOCK_FIRST_STEP = 'referral_email_block_first_step_text';
    const REFERRAL_EMAIL_BLOCK_FIRST_STEP_REFERRAL_PROMO_CODE = 'referral_email_block_first_step_text_referral_code';
    const REFERRAL_EMAIL_BLOCK_SECOND_STEP = 'referral_email_block_second_step_text';
    const REFERRAL_EMAIL_BLOCK_THIRD_STEP = 'referral_email_block_third_step_text';
    const REFERRAL_CLIENT_BLOCK_HEADING = 'referral_client_block_heading';
    const REFERRAL_CLIENT_BLOCK_SUBTITLE = 'referral_client_block_subtitle';
    const REFERRAL_CLIENT_BLOCK_MIN_AMOUNT = 'referral_client_block_min_amount';
    const UNSPENT_BALANCE_REMINDER_PERIOD_FIRST = 'unspent_balance_reminder_period_first';
    const UNSPENT_BALANCE_REMINDER_PERIOD_SECOND = 'unspent_balance_reminder_period_second';

    // how it works modal:
    public const HOW_IT_WORKS_FIRST_STEP_TIP_MESSAGE = 'how_it_works_first_step_tip_message';
    public const HOW_IT_WORKS_SECOND_STEP_TIP_MESSAGE = 'how_it_works_second_step_tip_message';
    public const HOW_IT_WORKS_THIRD_STEP_TIP_MESSAGE = 'how_it_works_third_step_tip_message';

    // #dashboard tab
    public const DASHBOARD_ONBOARDING_LABEL = 'dashboard_onboarding_label';
    public const DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL = 'total_cashback_amount_label';

    // #earn tab
    public const EARN_INVITE_AND_EARN_TITLE = 'earn_invite_and_earn_title';

    // #referrer-balance
    public const REFERRER_BALANCE_TIP_MESSAGE = 'referrer_balance_tip_message';
    public const REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE = 'referrer_balance_available_funds_tip_message';
    public const REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE = 'referrer_balance_total_earned_tip_message';

    // instagram action
    public const INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE = 'instagram_action_send_us_the_post';
    public const INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE = 'instagram_action_description_message';

    public const TERMS_OF_SERVICE_LINK = 'terms_of_service_link';
    public const ON_AUTH_OPENED_TAB_ID = 'on_auth_opened_tab_id';

    /**
     * @return \Illuminate\Support\Collection
     */
    public function registeredSettings()
    {
        return collect([
            // Partner::CUSTOMIZATION_PRIMARY_COLOR
            static::PRIMARY_COLOR => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Partner::CUSTOMIZATION_CLIENT_APP_TITLE
            static::CLIENT_APP_TITLE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Partner::CUSTOMIZATION_ACTIVATE_PLASTIC_CARD_BEFORE_OTHER_ACTIONS
            static::ACTIVATE_CARD_FIRST => [
                'allowed' => [1, 0],
                'default' => 0,
            ],

            // Partner::CUSTOMIZATION_DISCOUNT_CARD_INSTEAD_OF_LOYALTY_CARD
            static::USE_DISCOUNT_CARD => [
                'allowed' => [1, 0],
                'default' => 0,
            ],

            // Partner::CUSTOMIZATION_LOGO_PICTURE
            static::LOGO_PICTURE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Partner::CUSTOMIZATION_EMAIL_BALANCE_CHANGED_FIRST_LINE
            static::BALANCE_CHANGED_HEADING => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Partner::CUSTOMIZATION_EMAIL_BALANCE_CHANGED_FIRST_LINE
            static::BALANCE_CHANGED_HEADING_FIAT => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Блок "Больше друзей - больше баллов" - текст перед ссылкой.
            static::REFERRAL_EMAIL_BLOCK_CONTENT => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Блок "Больше друзей - больше баллов" - текст перед ссылкой.
            static::REFERRAL_EMAIL_BLOCK_CONTENT_FIAT => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Блок "Больше друзей - больше баллов" - первый пункт действий.
            static::REFERRAL_EMAIL_BLOCK_FIRST_STEP => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Блок "Больше друзей - больше баллов" - второй пункт действий.
            static::REFERRAL_EMAIL_BLOCK_SECOND_STEP => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Блок "Больше друзей - больше баллов" - третий пункт действий.
            static::REFERRAL_EMAIL_BLOCK_THIRD_STEP => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Секция "Пригласить друга" (клиент) - заголовок.
            static::REFERRAL_CLIENT_BLOCK_HEADING => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Секция "Пригласить друга" (клиент) - подтекст.
            static::REFERRAL_CLIENT_BLOCK_SUBTITLE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Секция "Пригласить друга" (клиент) - условие минимальной суммы.
            static::REFERRAL_CLIENT_BLOCK_MIN_AMOUNT => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Отправлять первое письмо с напоминаниями о накопленных баллах спустя х дней
            static::UNSPENT_BALANCE_REMINDER_PERIOD_FIRST => [
                'allowed' => ':any:',
                'default' => 30,
            ],

            // Отправлять второе письмо с напоминаниями о накопленных баллах спустя х дней
            static::UNSPENT_BALANCE_REMINDER_PERIOD_SECOND => [
                'allowed' => ':any:',
                'default' => 60,
            ],

            // Модальное окно "Как это работает" в секции Реферальная ссылка - 1 шаг
            static::HOW_IT_WORKS_FIRST_STEP_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Модальное окно "Как это работает" в секции Реферальная ссылка - 2 шаг
            static::HOW_IT_WORKS_SECOND_STEP_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Модальное окно "Как это работает" в секции Реферальная ссылка - 3 шаг
            static::HOW_IT_WORKS_THIRD_STEP_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Ссылка на "Как это работает" / Начните делиться ссылками на сервис, чтобы получить больше результатов!
            static::DASHBOARD_ONBOARDING_LABEL => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Заголовок над реф ссылкой / Поделитесь ссылкой на сервис и заработайте
            static::EARN_INVITE_AND_EARN_TITLE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Надпись в табе в дашборде - сумма полученного кэшбека
            static::DASHBOARD_TOTAL_CASHBACK_AMOUNT_LABEL => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Надпись в алерте на вкладке "Ваш баланс"
            static::REFERRER_BALANCE_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Тултип Доступно для вывода на вкладке "Ваш баланс"
            static::REFERRER_BALANCE_AVAILABLE_FUNDS_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Тултип всего заработано на вкладке "Ваш баланс"
            static::REFERRER_BALANCE_TOTAL_EARNED_TIP_MESSAGE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Ссылка на правила сервиса
            static::TERMS_OF_SERVICE_LINK => [
                'allowed' => ':any:',
                'default' => '',
            ],

            // Какую страницу открываем первой при авторизации
            static::ON_AUTH_OPENED_TAB_ID => [
                'allowed' => ':any:',
                'default' => 0,
            ],

            static::REFERRAL_EMAIL_BLOCK_CONTENT_REFERRAL_PROMO_CODE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            static::REFERRAL_EMAIL_BLOCK_FIRST_STEP_REFERRAL_PROMO_CODE => [
                'allowed' => ':any:',
                'default' => '',
            ],

            static::INSTAGRAM_ACTION_MODAL_SEND_US_THE_POST_MESSAGE => [
                'allowed' => ':any:',
                'default' => __('Send us the post'),
            ],

            static::INSTAGRAM_ACTION_MODAL_DESCRIPTION_MESSAGE => [
                'allowed' => ':any:',
                'default' => __('Artificial Intelligence will check the content of the image and give you a reward.'),
            ],
        ]);
    }
}
