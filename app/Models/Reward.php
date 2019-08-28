<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;

/**
 * Class Reward.
 *
 * @property int                $id
 * @property string             $type
 * @property float              $price
 * @property string             $price_type
 * @property float              $value
 * @property string             $title
 * @property string             $description
 * @property int                $partner_id
 * @property string             $value_type
 * @property Partner            $partner
 * @property array              $config
 * @property string             $status
 * @property string             $description_short
 * @property \Carbon\Carbon     $created_at
 * @property \Carbon\Carbon     $updated_at
 * @property SpecialOfferReward $specialOfferReward
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward wherePriceType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reward whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reward extends AbstractModel
{
    use CrudTrait;

    protected $table = 'rewards';

    private $rewardProcessor;

    const VALUE_TYPE_FIXED = 'fixed';
    const VALUE_TYPE_PERCENT = 'percent';

    const PRICE_TYPE_POINTS = 'points';
    const PRICE_TYPE_FIAT = 'fiat';

    const TYPE_GIFTD_DISCOUNT = 'GiftdDiscount';
    const TYPE_BITREWARDS_PAYOUT = 'BitrewardsPayout';
    const TYPE_FIAT_WITHDRAW = 'FiatWithdraw';

    const CONFIG_MIN_AMOUNT_TOTAL = 'min-amount-total';
    const CONFIG_LIFETIME = 'lifetime';

    const CONFIG_LEGACY_GIFTD_CARD_ID = 'giftd_card_id';
    const CONFIG_GIFTD_CARD_ID = 'giftd-card-id';
    const CONFIG_GIFTD_USER_ID = 'giftd-user-id';
    const CONFIG_GIFTD_API_KEY = 'giftd-api-key';
    const CONFIG_POINTS_TO_BRW_EXCHANGE_RATE = 'points-to-brw-exchange-rate';

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    public $timestamps = true;

    protected $fillable = [
        'type',
        'price',
        'price_type',
        'value',
        'value_type',
        'title',
        'description',
        'partner_id',
        'config',
        'status',
        'description_short',
        'tag',
    ];

    protected $casts = [
        'config' => 'array',
        'price' => 'int',
        'value' => 'float',
    ];

    protected $guarded = [];

    public static function valueTypes(): array
    {
        return [
            self::VALUE_TYPE_FIXED => __('Fixed'),
            self::VALUE_TYPE_PERCENT => __('Percent'),
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return \App\Services\RewardProcessors\Base
     */
    public function getRewardProcessor()
    {
        if (!$this->rewardProcessor) {
            $className = '\App\Services\RewardProcessors\\'.$this->type;
            $this->rewardProcessor = new $className($this);
        }

        return $this->rewardProcessor;
    }

    public function beforeSave()
    {
        if (null === $this->tag) {
            $this->tag = '';
        }
    }

    public function getGiftdCardId()
    {
        return
            $this->config[self::CONFIG_LEGACY_GIFTD_CARD_ID] ?? $this->config[self::CONFIG_GIFTD_CARD_ID] ?? null;
    }

    public function isArbitraryPriceAllowed()
    {
        return !(bool) $this->price;
    }

    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function specialOfferReward()
    {
        return $this->hasOne(SpecialOfferReward::class, 'reward_id');
    }

    public function isEnabled(): bool
    {
        return self::STATUS_ENABLED === $this->status;
    }

    public function enable(): self
    {
        $this->status = self::STATUS_ENABLED;

        return $this;
    }

    public function disable(): self
    {
        $this->status = self::STATUS_DISABLED;

        return $this;
    }
}
