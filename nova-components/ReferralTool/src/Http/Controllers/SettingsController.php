<?php

namespace Bitrewards\ReferralTool\Http\Controllers;

use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Services\Giftd\ApiClient;
use Bitrewards\DifferentiatedReferralCashback\DifferentiatedReferralCashback;
use Bitrewards\ReferralTool\Fields\FieldGroup;
use Bitrewards\ReferralTool\Traits\CustomFormTrait;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Timothyasp\Color\Color;

class SettingsController extends Controller
{
    use CustomFormTrait;

    public function settings(NovaRequest $request): FieldCollection
    {
        $fields = $this->settingFields($request);

        return $this->fillValues(
            $this->resolveFields($fields),
            $this->defaultSettings($request)
        );
    }

    public function saveSettings(NovaRequest $request)
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $formData = $this->formData($request, $this->settingFields($request), $this->defaultSettings($request));

        \DB::transaction(function () use ($partner, $formData) {
            if ($formData->hasChanged('referrer_reward_price_type', 'referrer_reward_amount')) {
                $orderReferralAction = $this->getOrderReferralAction($partner);

                if ($orderReferralAction) {
                    $orderReferralAction->value_type = $formData->get('referrer_reward_price_type');
                    $orderReferralAction->value = $formData->get('referrer_reward_amount');
                    $orderReferralAction->saveOrFail();
                }
            }

            if ($formData->hasChanged('referral_reward_price_type', 'referral_reward_amount', 'referral_minimum_order_amount')) {
                $orderReferralAction = $this->getOrderReferralAction($partner);

                if ($orderReferralAction) {
                    $orderReferralAction->setConfigOption(
                        Action::CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL,
                        $formData->get('referral_minimum_order_amount')
                    );
                    $orderReferralAction->setConfigOption(
                        Action::CONFIG_REFERRAL_REWARD_VALUE_TYPE,
                        $formData->get('referral_reward_price_type')
                    );
                    $orderReferralAction->setConfigOption(
                        Action::CONFIG_REFERRAL_REWARD_VALUE,
                        $formData->get('referral_reward_amount')
                    );
                    $orderReferralAction->saveOrFail();
                }
            }

            if ($formData->hasChanged('order_referral_action_config', 'order_referral_action_config_value_policy')) {
                $orderReferralAction = $this->getOrderReferralAction($partner);

                if ($orderReferralAction) {
                    $decoded = \HJson::decode($formData->get('order_referral_action_config'));
                    $decoded[Action::CONFIG_VALUE_POLICY] = json_decode($formData->get('order_referral_action_config_value_policy'), true);

                    if (is_null($decoded)) {
                        abort(400, __('Invalid JSON data'));
                    }
                    $orderReferralAction->config = $decoded;
                    $orderReferralAction->saveOrFail();
                }
            }

            if ($formData->hasChanged('appearance_title', 'appearance_color', 'registration_auto', 'registration_social')) {
                $partner->setSetting(Partner::CUSTOMIZATION_CLIENT_APP_TITLE, $formData->get('appearance_title'));
                $partner->setSetting(Partner::CUSTOMIZATION_PRIMARY_COLOR, $formData->get('appearance_color'));
                $partner->setSetting(Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS, (bool) $formData->get('registration_auto'));
                $partner->setSetting(Partner::SETTINGS_AUTH_VIA_SOCIAL_NETWORKS_HIDDEN, !$formData->get('registration_social'));
                $partner->saveOrFail();
            }

            if ($formData->hasChanged('ribbon-getreferrals-primary', 'customization_getreferrals_text')) {
                ApiClient::create($partner)->updatePartnerSettings(
                    $formData->getMultiple('ribbon-getreferrals-primary', 'customization_getreferrals_text')
                );
            }
        });
    }

    protected function getPartnerSettings(Partner $partner, bool $cache = true): array
    {
        static $settings;

        if ($cache && $settings) {
            return $settings;
        }

        $settings = ApiClient::create($partner)->getPartnerSettings();

        return $settings;
    }

    protected function settingFields(NovaRequest $request): array
    {
        $partner = $request->user()->partner;
        $fields = [];

        if ($this->getOrderReferralAction($partner)) {
            $fields[] = new FieldGroup(__('Referrers rewards'), [
                Select::make(__('Reward type'), 'referrer_reward_price_type')
                      ->options(Reward::valueTypes())
                      ->rules([
                          'required',
                      ]),

                Number::make(__('Reward amount'), 'referrer_reward_amount'),
//                Number::make(__('Referral widget share amount'), 'referrer_reward_shared_amount'),
            ]);

            $fields[] = new FieldGroup(__('Referrals rewards'), [
                Select::make(__('Reward type'), 'referral_reward_price_type')
                      ->options(Reward::valueTypes()),

                Number::make(__('Reward amount'), 'referral_reward_amount'),
                Number::make(__('Minimal order value'), 'referral_minimum_order_amount'),
            ]);
        }

        $fields[] = new FieldGroup(__('Appearance settings'), [
            Text::make(__('Title'), 'appearance_title'),
            Color::make(__('Color'), 'appearance_color')->slider(),
        ]);

        $fields[] = new FieldGroup(__('Registration'), [
            Boolean::make(__('Auto registration every customer'), 'registration_auto'),
            Boolean::make(__('Allow to register with social networks'), 'registration_social'),
        ]);

        $partnerSettings = $this->getPartnerSettings($partner);

        if (isset($partnerSettings['ribbon-getreferrals-primary'])) {
            $fields[] = new FieldGroup(__('Notification button'), [
                Text::make(__('Change the title on the notification tool'), 'customization_getreferrals_text'),
                Boolean::make(__('Show the notification tool'), 'ribbon-getreferrals-primary'),
            ]);
        }

        $advancedSettings = [];

        if ($this->getOrderReferralAction($partner)) {
            $advancedSettings[] = Code::make(__('Referral rewards advanced config'), 'order_referral_action_config')->json()->help(
                __('Edit this JSON only if you understand what you are doing. Your loyalty system may stop working if you enter incorrect data in this field.')
            );
        }

        $fields[] = new FieldGroup(__('Гибкий кэшбек для реферера'), [
            DifferentiatedReferralCashback::make('Settings', 'order_referral_action_config_value_policy'),
        ]);

        $fields[] = new FieldGroup(__('Advanced settings').' 🤓', $advancedSettings);

        return $fields;
    }

    protected function defaultSettings(NovaRequest $request): array
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $partnerSettings = $this->getPartnerSettings($partner);
        $defaults = [];

        if (isset($partnerSettings['ribbon-getreferrals-primary'])) {
            $defaults = $partnerSettings;
        }

        $defaults += [
            'appearance_title' => $partner->getSetting(Partner::CUSTOMIZATION_CLIENT_APP_TITLE),
            'appearance_color' => $partner->getSetting(Partner::CUSTOMIZATION_PRIMARY_COLOR),

            'registration_auto' => (bool) $partner->getSetting(Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS),
            'registration_social' => !$partner->getSetting(Partner::SETTINGS_AUTH_VIA_SOCIAL_NETWORKS_HIDDEN),

            'notification_show_tool' => null,
            'notification_change_title' => null,

            'referral_widget_show' => null,
        ];

        $orderReferralAction = $this->getOrderReferralAction($partner);

        if ($orderReferralAction) {
            $defaults += [
                'referral_reward_price_type' => $orderReferralAction->getConfigOption(Action::CONFIG_REFERRAL_REWARD_VALUE_TYPE),
                'referral_reward_amount' => $orderReferralAction->getConfigOption(Action::CONFIG_REFERRAL_REWARD_VALUE),
                'referral_minimum_order_amount' => $orderReferralAction->getConfigOption(Action::CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL),

                'referrer_reward_price_type' => $orderReferralAction->value_type ?? null,
                'referrer_reward_amount' => $orderReferralAction->value ?? null,
                'referrer_reward_shared_amount' => null,

                'referral_reward_config' => \HJson::encode($orderReferralAction->config ?: []),

                'order_referral_action_config' => \HJson::encode($orderReferralAction->config ?: []),

                'order_referral_action_config_value_policy' => $orderReferralAction->getValuePolicy() ?? null,
            ];
        }

        return $defaults;
    }

    protected function getOrderReferralAction(Partner $partner): ?Action
    {
        return $partner
            ->actions
            ->where('type', Action::TYPE_ORDER_REFERRAL)
            ->first();
    }

    protected function getOrderCashbackAction(Partner $partner): ?Action
    {
        return $partner
            ->actions
            ->where('type', Action::TYPE_ORDER_CASHBACK)
            ->first();
    }
}
