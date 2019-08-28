<?php

namespace App\Models;

use App\Crud\Traits\EditableJsonFieldsCrudTrait;
use App\DTO\ActionValuePolicyRule;
use App\Services\Giftd\GiftdService;

/**
 * Class Action.
 *
 * @property int                $id
 * @property string             $type
 * @property float              $value
 * @property string             $value_type
 * @property string             $title
 * @property string             $description
 * @property array              $config
 * @property int                $partner_id
 * @property int                $limit_per_user
 * @property int                $limit_min_time_between
 * @property string             $status
 * @property bool               $is_system
 * @property \Carbon\Carbon     $created_at
 * @property \Carbon\Carbon     $updated_at
 * @property Partner            $partner
 * @property SpecialOfferAction $specialOfferAction
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereValueType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereSourceOrderMinAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Action whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Action extends AbstractModel
{
    use EditableJsonFieldsCrudTrait;

    protected $table = 'actions';

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLED = 'disabled';

    public const VALUE_TYPE_FIXED = 'fixed';
    public const VALUE_TYPE_PERCENT = 'percent';
    public const VALUE_TYPE_FIXED_FIAT = 'fiat';

    public const CONFIG_SOURCE_ORDER_MIN_AMOUNT = 'source-order-min-amount';
    public const CONFIG_REFERRAL_REWARD_VALUE = 'referral-reward-value';
    public const CONFIG_REFERRAL_REWARD_VALUE_TYPE = 'referral-reward-value-type';
    public const CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL = 'referral-reward-min-amount-total';
    public const CONFIG_REFERRAL_REWARD_LIFETIME = 'referral-reward-lifetime';

    public const CONFIG_CARD_ID = 'card_id';
    public const CONFIG_ADMITAD_OFFER_ID = 'admitad-offer-id';

    public const CONFIG_VALUE_POLICY = 'value-policy';
    public const CONFIG_POINTS_LIFETIME = 'points-lifetime';

    public const CONFIG_VALUE_OVERRIDE_FOR_PRODUCTS = 'value-override-for-products';

    public const FULFILLMENT_TYPE_TEXT = 'text';
    public const FULFILLMENT_TYPE_URL = 'url';
    public const FULFILLMENT_TYPE_REFERRAL = 'referral';

    public const TYPE_ORDER_CASHBACK = 'OrderCashback';
    public const TYPE_ORDER_REFERRAL = 'OrderReferral';
    public const TYPE_SIGNUP = 'Signup';
    public const TYPE_CUSTOM_BONUS = 'CustomBonus';
    public const TYPE_JOIN_FB = 'JoinFb';
    public const TYPE_JOIN_VK = 'JoinVk';
    public const TYPE_SHARE_VK = 'ShareVk';
    public const TYPE_SHARE_FB = 'ShareFb';
    public const TYPE_REFILL_BIT = 'RefillBit';
    public const TYPE_EXCHANGE_ETH_TO_BIT = 'ExchangeEthToBit';
    public const TYPE_AFFILIATE_ACTION_ADMITAD = 'AffiliateActionAdmitad';
    public const TYPE_SHARE_INSTAGRAM = 'ShareInstagram';
    public const TYPE_SUBSCRIBE_TO_TELEGRAM = 'SubscribeTelegram';
    public const TYPE_CUSTOM_SOCIAL_ACTION = 'CustomSocialAction';

    protected $casts = [
        'config' => 'array',
        'value' => 'float',
        'is_system' => 'boolean',
    ];

    public $timestamps = true;

    private $actionProcessor = null;

    protected $editableJsonFields = [
        'config' => [
            self::CONFIG_REFERRAL_REWARD_VALUE,
            self::CONFIG_REFERRAL_REWARD_VALUE_TYPE,
            self::CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL,
        ],
    ];

    protected $fillable = [
        'type',
        'value',
        'value_type',
        'title',
        'description',
        'partner_id',
        'status',
        'is_system',
        'config',
        'tag',
        'limit_per_user',
    ];

    protected $guarded = [];

    /**
     * @return \App\Services\ActionProcessors\Base|
     */
    public function getActionProcessor()
    {
        if (!$this->actionProcessor) {
            $className = '\App\Services\ActionProcessors\\'.$this->type;
            $this->actionProcessor = new $className($this);
        }

        return $this->actionProcessor;
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function beforeSave()
    {
        if (null === $this->tag) {
            $this->tag = '';
        }
    }

    public function afterSave()
    {
        if ($this->type === static::TYPE_ORDER_REFERRAL) {
            // update referral widget and shares in giftd
            $giftdService = app(GiftdService::class);

            $giftdService->updateReferralWidget($this->partner);
        }
    }

    public function getGiftdCardId()
    {
        return $this->config[self::CONFIG_CARD_ID] ?? null;
    }

    public function isOrderBased()
    {
        return self::TYPE_ORDER_CASHBACK == $this->type || self::TYPE_ORDER_REFERRAL == $this->type;
    }

    public function getCorrespondingStoreEventType()
    {
        switch ($this->type) {
            case Action::TYPE_JOIN_FB:
                return StoreEvent::ACTION_JOIN_FB;

            case Action::TYPE_JOIN_VK:
                return StoreEvent::ACTION_JOIN_VK;

            case Action::TYPE_SHARE_FB:
                return StoreEvent::ACTION_SHARE_FB;

            case Action::TYPE_SHARE_VK:
                return StoreEvent::ACTION_SHARE_VK;

            default:
                return null;
        }
    }

    public function specialOfferAction()
    {
        return $this->hasOne(SpecialOfferAction::class, 'action_id');
    }

    public function hasValuePolicy()
    {
        return (bool) $this->getValuePolicy();
    }

    /**
     * @return array|null
     */
    public function getValuePolicy()
    {
        $policy = $this->config[self::CONFIG_VALUE_POLICY] ?? null;

        if (empty($policy)) {
            return null;
        }

        try {
            return array_map(function ($item) {
                return new ActionValuePolicyRule(
                    $item['condition'] ?? [],
                    $item['valueType'] ?? null,
                    $item['value'] ?? null
                );
            }, $policy);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function isExpiringAction(): bool
    {
        return !empty($this->config[self::CONFIG_POINTS_LIFETIME]);
    }

    public function getPointsLifeTime(): ?int
    {
        if ($this->isExpiringAction()) {
            return (int) $this->config[self::CONFIG_POINTS_LIFETIME];
        }

        return null;
    }

    public function getConfigOption(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function setConfigOption(string $key, $value): void
    {
        $this->setAttribute('config', [$key => $value] + $this->config);
    }
}
