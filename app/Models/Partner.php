<?php

namespace App\Models;

use App\Administrator;
use App\Db\Traits\SaveHooks;
use App\Services\ActionProcessors\Base;
use App\Services\ActionProcessors\OrderCashback;
use App\Services\ActionProcessors\OrderReferral;
use App\Services\ActionService;
use App\Services\PartnerService;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelPropertyBag\Settings\HasSettings;

/**
 * Class Partner.
 *
 * @property int            $id
 * @property string         $title
 * @property string         $email
 * @property int            $giftd_id
 * @property int            $giftd_user_id
 * @property string         $giftd_api_key
 * @property mixed          $customizations
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Administrator  $mainAdministrator
 * @property int            $currency
 * @property string         $url
 * @property float          $money_to_points_multiplier
 * @property array          $partner_settings
 * @property string         $default_language
 * @property string         $default_country
 * @property string         $eventbrite_oauth_token
 * @property string         $eventbrite_url
 * @property string         $eth_address
 * @property string         $withdraw_key
 * @property string         $mainAdministratorApiKey
 * @property string         $key
 * @property float          $balance
 * @property Collection     $deposits
 * @property int            $partner_group_id
 * @property PartnerGroup   $partnerGroup
 * @property string         $partner_group_role
 * @property Collection     $actions
 * @property Collection     $storeEntities
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereGiftdId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereCustomizations($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Partner whereKey($value)
 * @mixin \Eloquent
 */
class Partner extends AbstractModel
{
    use CrudTrait;
    use SaveHooks;
    use HasSettings;

    public const CUSTOMIZATION_PRIMARY_COLOR = 'primary-color';
    public const CUSTOMIZATION_CLIENT_APP_TITLE = 'client-app-title';
    public const CUSTOMIZATION_ACTIVATE_PLASTIC_CARD_BEFORE_OTHER_ACTIONS = 'activate-plastic-card-before-other-actions';
    public const CUSTOMIZATION_DISCOUNT_CARD_INSTEAD_OF_LOYALTY_CARD = 'discount-card-instead-of-loyalty-card';
    public const CUSTOMIZATION_LOGO_PICTURE = 'logo-picture';
    public const CUSTOMIZATION_EMAIL_BALANCE_CHANGED_FIRST_LINE = 'email-balance-changed-first-line';

    public const SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS = 'auto-signup-users-from-orders';
    public const SETTINGS_AUTO_CONFIRM_INTERVAL = 'auto-confirm-interval';
    public const SETTINGS_AUTH_METHOD = 'auth-method';
    public const SETTINGS_BITREWARDS_ENABLED = 'bitrewards-enabled';
    public const SETTINGS_BITREWARDS_BRAND_FORCED = 'is-bitrewards-brand-forced';
    public const SETTINGS_BITRIX_ALLOW_DF_STATUS = 'bitrix-allow-df-status';

    public const SETTINGS_IS_OBOROT_PROMO = 'is-oborot-promo';
    public const SETTINGS_IS_BAZELEVS = 'is-bazelevs';
    public const SETTINGS_IS_AVTOCOD = 'is-avtocod';
    public const SETTINGS_IS_MIGOFF = 'is-migoff';
    public const SETTINGS_IS_FIGHTWEAR_PROMO = 'is-fightwear-promo';
    public const SETTINGS_IS_DELIVERED_STATUS_ENOUGH_FOR_ORDER_AUTO_CONFIRM = 'is-delivered-status-enough-for-auto-confirm';

    public const SETTINGS_EVENTBRITE_AUTO_CONFIRM_TRANSACTIONS_AFTER_EVENT_START_INTERVAL = 'eventbrite.auto-confirm-transactions-after-event-start-interval';

    public const AUTH_METHOD_EMAIL = 'email';
    public const AUTH_METHOD_PHONE = 'phone';

    public const CODE_BITREWARDS = 'bitrewards';

    public const BIT_WITHDRAWAL_FEE_TYPE_PERCENT = 'percent';
    public const BIT_WITHDRAWAL_FEE_TYPE_FIXED = 'fixed';

    public const SETTINGS_BIT_WITHDRAWAL_FEE_TYPE = 'bit-withdrawal-fee-type';
    public const SETTINGS_BIT_WITHDRAWAL_FEE = 'bit-withdrawal-fee';
    public const SETTINGS_BIT_MIN_WITHDRAWAL = 'bit-min-withdrawal';

    public const FIAT_WITHDRAW_FEE_TYPE_PERCENT = 'percent';
    public const FIAT_WITHDRAW_FEE_TYPE_FIXED = 'fixed';

    public const SETTINGS_FIAT_WITHDRAW_MIN_AMOUNT = 'fiat-withdrawal-min';
    public const SETTINGS_FIAT_WITHDRAW_MAX_AMOUNT = 'fiat-withdrawal-max';
    public const SETTINGS_FIAT_WITHDRAW_FEE = 'fiat-withdrawal-fee';
    public const SETTINGS_FIAT_WITHDRAW_FEE_TYPE = 'fiat-withdrawal-fee-type';
    public const SETTINGS_FIAT_WITHDRAW_LOGIN_TITLE = 'fiat-withdrawal-login-title';
    public const SETTINGS_FIAT_WITHDRAW_INVITE_TITLE = 'fiat-withdrawal-invite-title';
    public const SETTINGS_CUSTOM_INLINE_CSS = 'custom-inline-css';

    public const SETTINGS_IS_EARN_BIT_HIDDEN = 'is-earn-bit-hidden';
    public const SETTINGS_IS_SPEND_BIT_HIDDEN = 'is-spend-bit-hidden';
    public const SETTINGS_IS_INVITE_FRIENDS_HIDDEN = 'is-invite-friends-hidden';
    public const SETTINGS_IS_ACTIVATE_PLASTIC_HIDDEN = 'is-activate-plastic-hidden';
    public const SETTINGS_IS_ENTER_PROMOCODE_HIDDEN = 'is-enter-promocode-hidden';
    public const SETTINGS_IS_MY_COUPONS_HIDDEN = 'is-my-coupons-hidden';
    public const SETTINGS_IS_LOGOUT_BUTTON_HIDDEN = 'is-logout-button-hidden';
    public const SETTINGS_IS_POPUP_CLOSE_BUTTON_HIDDEN = 'is-popup-close-button-hidden';
    public const SETTINGS_IS_EDIT_PROFILE_BUTTON_HIDDEN = 'is-edit-profile-button-hidden';
    public const SETTINGS_IS_CLIENT_REFERRAL_HEADING_HIDDEN = 'is-сlient-referral-heading-hidden';
    public const SETTINGS_IS_CASHIER_ENABLED_FOR_BITREWARDS = 'is-cashier-enabled-for-bitrewards';
    public const SETTINGS_IS_FIAT_REFERRAL_ENABLED = 'is-fiat-referral-enabled';
    public const SETTINGS_IS_REFERRAL_LINK_ENABLED = 'is-referral-link-enabled';
    public const SETTINGS_IS_REFERRAL_PROMO_CODE_ENABLED = 'is-referral-promo-code-enabled';
    public const SETTINGS_IS_ALL_NOTIFICATIONS_DISABLED = 'is-all-notifications-disabled';
    public const SETTINGS_IS_WITHDRAW_DISABLED = 'is-withdraw-disabled';
    public const SETTINGS_EARN_MESSAGE = 'earn-message';
    public const SETTINGS_SPEND_MESSAGE = 'spend-message';
    public const SETTINGS_EARN_TIP_MESSAGE = 'earn-tip-message';
    public const SETTINGS_SPEND_TIP_MESSAGE = 'spend-tip-message';
    public const SETTINGS_IS_GRADED_PERCENT_REWARD_MODE_ENABLED = 'is-graded-percent-reward-mode-enabled';
    public const SETTINGS_IS_GOOGLE_ANALYTICS_DISABLED = 'disable-google-analytics';
    public const SETTINGS_IS_EMAIL_AUTO_LOGIN_DISABLED = 'is-email-auto-login-disabled';
    public const SETTINGS_HIDDEN_ACTIONS = 'hidden-actions';

    public const SETTINGS_IS_MERGE_BALANCES_ENABLED = 'is-merge-balances-enabled';
    public const SETTINGS_IS_CUSTOM_BONUS_EMAIL_DISABLED = 'is-custom-bonus-email-disabled';

    public const SETTINGS_IS_PUSHING_TO_EXTERNAL_EMAIL_SERVICES_ENABLED = 'is-pushing-to-external-email-services-enabled';

    public const SETTINGS_BURN_POINT_NOTIFY_WEEKDAY = 'burn-point-notify-weekday';
    public const SETTINGS_BURN_POINT_NOTIFY_TIME = 'burn-point-notify-time';

    public const SETTINGS_IS_HOW_IT_WORKS_HIDDEN = 'is-how-it-works-hidden';
    public const SETTINGS_IS_SIGNUP_DISABLED = 'is-signup-disabled';
    public const SETTINGS_AUTH_VIA_SOCIAL_NETWORKS_HIDDEN = 'is-auth-via-social-networks-hidden';
    public const SETTINGS_BRAND_URL = 'brand-url';
    public const SETTINGS_IS_MENU_FONT_BOLDER = 'is-menu-font-bolder';

    public const PARTNER_GROUP_ROLE_PARTNER = 'partner';
    public const PARTNER_GROUP_ROLE_ADMIN = 'admin';

    const DEFAULT_COUNTRY = 'ru';

    public static function getAuthMethodList()
    {
        return [
            self::AUTH_METHOD_EMAIL => __('Email'),
            self::AUTH_METHOD_PHONE => __('Phone'),
        ];
    }

    protected $table = 'partners';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'email',
        'giftd_id',
        'giftd_user_id',
        'giftd_api_key',
        'customizations',
        'money_to_points_multiplier',
        'url',
        'default_language',
        'partner_settings',
        'default_country',
        'partner_group_role',
    ];

    protected $guarded = [];

    protected $casts = [
        'customizations' => 'array',
        'partner_settings' => 'array',
    ];

    public function beforeSave()
    {
        if (!$this->key) {
            $this->key = !Partner::count() ? 'test-partner-key' : Str::lower(Str::random(10));
        }

        if ($this->default_country) {
            $this->default_country = mb_strtolower(mb_substr($this->default_country, 0, 2));
        } else {
            $this->default_country = self::DEFAULT_COUNTRY;
        }
    }

    /**
     * @return OrderReferral|Base|null
     */
    public function getOrderReferralActionProcessor()
    {
        $action = Action::model()->whereAttributes([
            'partner_id' => $this->id,
            'type' => Action::TYPE_ORDER_REFERRAL,
            'status' => Action::STATUS_ENABLED,
        ])->first();

        return $action ? $action->getActionProcessor() : null;
    }

    /**
     * @return OrderCashback|null
     */
    public function getOrderCashbackActionProcessor()
    {
        $action = Action::model()->whereAttributes([
            'partner_id' => $this->id,
            'type' => Action::TYPE_ORDER_CASHBACK,
            'status' => Action::STATUS_ENABLED,
        ])->first();

        return $action ? $action->getActionProcessor() : null;
    }

    public function isConnectedToGiftdApi()
    {
        return $this->giftd_api_key && $this->giftd_user_id;
    }

    public function mainAdministrator()
    {
        return $this
            ->belongsTo(Administrator::class, 'id', 'partner_id')
            ->where('is_main', true);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function partnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    /**
     * @return HasMany
     */
    public function helpItems()
    {
        return $this->hasMany(HelpItem::class);
    }

    public function getCustomization($key, $default = null)
    {
        return $this->customizations[$key] ?? $default;
    }

    public function findByKey($key)
    {
        $result = Partner::where(['key' => $key])->first();

        return $result;
    }

    public function findByGiftdUserId($id)
    {
        $result = Partner::where(['giftd_user_id' => $id])->first();

        return $result;
    }

    /**
     * @param $type
     *
     * @return Action|null
     */
    public function findActionByType($type)
    {
        return Action::model()->whereAttributes([
            'partner_id' => $this->id,
            'type' => $type,
        ])->orderBy('id', 'asc')->first();
    }

    public function setSetting($path, $value)
    {
        $settings = $this->partner_settings;
        array_set($settings, $path, $value);
        $this->partner_settings = $settings;
    }

    public function getSetting($path, $default = null)
    {
        return array_get($this->partner_settings, $path, $default);
    }

    public function getEntityAutoFinishInterval($entityType)
    {
        return $this->getSetting(self::SETTINGS_AUTO_CONFIRM_INTERVAL.'.'.$entityType);
    }

    public function getSignupBonus()
    {
        $action = $this->findActionByType(Action::TYPE_SIGNUP);
        $result = 0;

        if ($action) {
            $result = Action::VALUE_TYPE_FIXED == $action->value_type ? $action->value : 0;
        }

        return $result;
    }

    public function getFiatReferralBonusStr(): ?string
    {
        $actionService = app(ActionService::class);

        $action = $actionService->getReferralAction($this);

        if (!$action) {
            return null;
        }

        return \HAction::getRewardStr($action);
    }

    /**
     * @param $type
     *
     * @return Reward
     */
    public function getRewardByType($type)
    {
        return Reward::model()->whereAttributes([
            'partner_id' => $this->id,
            'type' => $type,
        ])->orderBy('id', 'asc')->first();
    }

    public function getSignupAction()
    {
        return $this->findActionByType(Action::TYPE_SIGNUP);
    }

    public function getAuthMethod()
    {
        return $this->getSetting(self::SETTINGS_AUTH_METHOD, self::AUTH_METHOD_EMAIL);
    }

    public function isAuthMethodEmail()
    {
        return self::AUTH_METHOD_EMAIL == $this->getAuthMethod();
    }

    public function isAuthMethodPhone()
    {
        return self::AUTH_METHOD_PHONE == $this->getAuthMethod();
    }

    public function hasEventbrite()
    {
        return $this->eventbrite_oauth_token && $this->eventbrite_url;
    }

    public function getDefaultCustomBonusAction()
    {
        return $this->findActionByType(Action::TYPE_CUSTOM_BONUS);
    }

    public function filterActions(\Closure $callback = null)
    {
        if (is_null($callback)) {
            $callback = function ($query) {
                return $query;
            };
        }

        return $callback($this->actions()->getQuery())->get();
    }

    /**
     * Get partner Actions with "CustomBonus" type.
     *
     * @return \Illuminate\Support\Collection
     */
    public function customBonusActions()
    {
        $actions = $this->filterActions(function (Builder $query) {
            return $query->with('partner')->where('type', Action::TYPE_CUSTOM_BONUS);
        });

        if (1 === count($actions)) {
            return collect([]);
        }

        return $actions;
    }

    public function isBitrewardsEnabled(): bool
    {
        return
            (\App::isLocal() && ($_REQUEST['enable-bitrewards'] ?? false)) ||
            (bool) $this->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED);
    }

    public function isBitRewardsBrandForced(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED) ||
            (bool) $this->getSetting(Partner::SETTINGS_BITREWARDS_BRAND_FORCED);
    }

    public function isBitrewardsDemoPartner()
    {
        // a bit hacky but I'm coding at airplane and can't do much
        return
            $this->isBitrewardsEnabled() &&
            $this->created_at->getTimestamp() < 1516807384;
    }

    /**
     * @return bool
     */
    public function hasTreasuryWallet()
    {
        return !empty($this->eth_address);
    }

    public function getBitWithdrawFeeType()
    {
        return $this->getSetting(Partner::SETTINGS_BIT_WITHDRAWAL_FEE_TYPE) ?? Partner::BIT_WITHDRAWAL_FEE_TYPE_PERCENT;
    }

    public function getBitWithdrawMinAmount()
    {
        $setting = $this->getSetting(Partner::SETTINGS_BIT_MIN_WITHDRAWAL);

        if ($setting) {
            return $setting;
        }

        $amount = 1;

        $actions = Action::model()
            ->whereAttributes([
                'partner_id' => $this->id,
                'value_type' => Action::VALUE_TYPE_FIXED,
            ])
            ->whereIn('type', [Action::TYPE_JOIN_FB, Action::TYPE_JOIN_VK, Action::TYPE_SHARE_FB, Action::TYPE_SHARE_VK, Action::TYPE_SIGNUP])
            ->get();

        foreach ($actions as $action) {
            $amount += $action->value;
        }

        return $amount;
    }

    public function getBitWithdrawFee()
    {
        return $this->getSetting(Partner::SETTINGS_BIT_WITHDRAWAL_FEE) ?? 30;
    }

    public function getBitWithdrawFeeForAmount($amount)
    {
        $withdrawFeeType = $this->getBitWithdrawFeeType();
        $withdrawFeeValue = $this->getBitWithdrawFee();

        if ($withdrawFeeType === static::BIT_WITHDRAWAL_FEE_TYPE_PERCENT) {
            $fee = ceil($amount / 100 * $withdrawFeeValue);
        } else {
            $fee = $withdrawFeeValue;
        }

        return $fee;
    }

    public function isMultiAccountEnabled(): bool
    {
        return $this->isBitrewardsEnabled();
    }

    public function isFiatReferralEnabled(?bool $default = null): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_FIAT_REFERRAL_ENABLED, $default);
    }

    public function isMergeBalancesEnabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_MERGE_BALANCES_ENABLED);
    }

    public function isWithdrawDisabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_WITHDRAW_DISABLED, false);
    }

    /**
     * @return bool
     */
    public function isReferralLinkEnabled(): bool
    {
        return (bool) ($this->getSetting(Partner::SETTINGS_IS_REFERRAL_LINK_ENABLED) ?? true);
    }

    /**
     * @return bool
     */
    public function isReferralPromoCodeEnabled(): bool
    {
        return (bool) ($this->getSetting(Partner::SETTINGS_IS_REFERRAL_PROMO_CODE_ENABLED) ?? true);
    }

    public function isOborotPromoPartner(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_OBOROT_PROMO);
    }

    public function isFightwearPromoPartner(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_FIGHTWEAR_PROMO);
    }

    public function isBazelevsPartner(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_BAZELEVS);
    }

    public function isAvtocodPartner(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_AVTOCOD);
    }

    public function isDeliveredStatusEnoughForOrderAutoConfirm(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_DELIVERED_STATUS_ENOUGH_FOR_ORDER_AUTO_CONFIRM);
    }

    public function isMigoffPartner(): bool
    {
        return (bool) $this->getSetting(self::SETTINGS_IS_MIGOFF);
    }

    public function getFiatWithdrawMinAmount($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_MIN_AMOUNT, $default);
    }

    public function getFiatWithdrawMaxAmount($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_MAX_AMOUNT, $default);
    }

    public function getFiatWithdrawFee($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_FEE, $default);
    }

    public function getFiatWithdrawFeeType($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_FEE_TYPE, $default);
    }

    public function getFiatWithdrawLoginTitle($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_LOGIN_TITLE, $default);
    }

    public function getFiatWithdrawInviteTitle($default = null)
    {
        return $this->getSetting(self::SETTINGS_FIAT_WITHDRAW_INVITE_TITLE, $default);
    }

    public function getCustomInlineCss($default = null)
    {
        return $this->getSetting(self::SETTINGS_CUSTOM_INLINE_CSS, $default);
    }

    public function afterSave(): void
    {
        $partnerService = app(PartnerService::class);

        if ($this->isFiatReferralEnabled()) {
            $partnerService->enableFiatReferral($this);
        } else {
            $partnerService->disableFiatReferral($this);
        }
    }

    public function getMainAdministratorApiKeyAttribute(): ?string
    {
        return $this->mainAdministrator->api_token;
    }

    public function getAppRootUrlAttribute()
    {
        return routePartner($this, 'client.index');
    }

    public function isSignupDisabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_SIGNUP_DISABLED);
    }

    public function isCustomBonusEmailDisabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_CUSTOM_BONUS_EMAIL_DISABLED);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return bool
     */
    public function isGradedPercentRewardModeEnabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_GRADED_PERCENT_REWARD_MODE_ENABLED, false);
    }

    /**
     * @return bool
     */
    public function isGoogleAnalyticsDisabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_GOOGLE_ANALYTICS_DISABLED, false);
    }

    /**
     * @return bool
     */
    public function isEmailAutoLoginDisabled(): bool
    {
        return (bool) $this->getSetting(Partner::SETTINGS_IS_EMAIL_AUTO_LOGIN_DISABLED, false);
    }

    /**
     * @return array
     */
    public function getHiddenActions(): array
    {
        return (array) $this->getSetting(Partner::SETTINGS_HIDDEN_ACTIONS, []);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(PartnerDeposit::class);
    }

    public function storeEntities()
    {
        return $this->hasMany(StoreEntity::class);
    }
}
