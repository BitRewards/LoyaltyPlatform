<?php

namespace App\Models;

use App\DTO\TransactionData;
use Backpack\CRUD\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction.
 *
 * @property int                      $id
 * @property float                    $balance_change
 * @property float                    $balance_before
 * @property float                    $balance_after
 * @property string                   $comment
 * @property string                   $status
 * @property \Carbon\Carbon           $confirmed_at
 * @property int                      $action_id
 * @property int                      $reward_id
 * @property int                      $source_order_id
 * @property int                      $source_store_entity_id
 * @property int                      $source_store_event_id
 * @property int                      $user_id
 * @property int                      $partner_id
 * @property int                      $actor_id
 * @property \App\DTO\TransactionData $data
 * @property User                     $user
 * @property Action                   $action
 * @property Reward                   $reward
 * @property Partner                  $partner
 * @property User                     $actor
 * @property StoreEvent               $sourceStoreEvent
 * @property StoreEntity              $sourceStoreEntity
 * @property \Carbon\Carbon           $created_at
 * @property \Carbon\Carbon           $updated_at
 * @property \Carbon\Carbon           $rejected_at
 * @property float|null               $output_balance
 * @property \Carbon\Carbon|null      $output_balance_expires_at
 * @property string                   $type
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereBalanceChange($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereConfirmationTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereRewardId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereSourceOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereConfirmedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereSourceStoreEventId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction whereSourceStoreEntityId($value)
 */
class Transaction extends AbstractModel
{
    use CrudTrait;

    protected $table = 'transactions';

    public const TYPE_REWARD = 'reward';
    public const TYPE_ACTION = 'action';
    public const TYPE_EXPIRATION = 'expiration';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';

    public const DATA_NOT_USABLE_BY_CLIENT = 'notUsableByClient';
    public const DATA_LIFETIME_OVERRIDDEN = 'lifetimeOverridden';
    public const DATA_PROMO_CODE = 'promo_code';
    public const DATA_EXPIRES = 'expires';
    public const DATA_URL = 'url';
    public const DATA_COMMENT = 'comment';
    public const DATA_TAG = 'tag';
    public const DATA_REWARD_EXECUTION_STARTED_AT = 'rewardExecutionStartedAt';
    public const DATA_REWARD_EXECUTION_FINISHED_AT = 'rewardExecutionFinishedAt';

    public const DATA_FIAT_WITHDRAW_AMOUNT = 'fiatWithdrawAmount';
    public const DATA_FIAT_WITHDRAW_FEE = 'fiatWithdrawFee';
    public const DATA_FIAT_WITHDRAW_FEE_TYPE = 'fiatWithdrawFeeType';
    public const DATA_FIAT_WITHDRAW_FEE_VALUE = 'fiatWithdrawFeeValue';
    public const DATA_FIAT_WITHDRAW_CARD_NUMBER = 'fiatWithdrawCardNumber';
    public const DATA_FIAT_WITHDRAW_FIRST_NAME = 'fiatWithdrawFirstName';
    public const DATA_FIAT_WITHDRAW_LAST_NAME = 'fiatWithdrawLastName';
    public const DATA_FIAT_WITHDRAW_EXTERNAL_OPERATION_ID = 'fiatWithdrawExternalOperationId';
    public const DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_BEFORE = 'fiatWithdrawMerchantBalanceBefore';
    public const DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_AFTER = 'fiatWithdrawMerchantBalanceAfter';
    public const DATA_FIAT_WITHDRAW_COMMENT = 'fiatWithdrawComment';

    public const DATA_ETHEREUM_ADDRESS = 'ethereumAddress';
    public const DATA_POINTS_TO_SPEND = 'pointsToSpend';
    public const DATA_MAGIC_NUMBER = 'magicNumber';
    public const DATA_CHILD_REFILL_BIT_TRANSACTION_ID = 'childRefillBitTransactionID';

    public const DATA_BITREWARDS_PAYOUT_AMOUNT = 'bitrewardsPayoutAmount';
    public const DATA_BITREWARDS_WITHDRAW_FEE = 'bitrewardsWithdrawFee';
    public const DATA_BITREWARDS_WITHDRAW_FEE_TYPE = 'bitrewardsWithdrawFeeType';
    public const DATA_BITREWARDS_WITHDRAW_FEE_VALUE = 'bitrewardsWithdrawFeeValue';
    public const DATA_TREASURY_RESPONSE = 'treasury_response';
    public const DATA_TREASURY_TX_HASH = 'treasury_tx_hash';
    public const DATA_TREASURY_DATA = 'treasury_tx_data';
    public const DATA_TREASURY_SENDER_ADDRESS = 'treasury_tx_sender';

    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
        'balance_change' => 'float',
        'actor_id' => 'integer',
        'balance_before' => 'float',
        'balance_after' => 'float',
        'output_balance' => 'float',
    ];

    protected $fillable = [
        'balance_change',
        'balance_before',
        'balance_after',
        'comment',
        'status',
        'confirmed_at',
        'action_id',
        'reward_id',
        'source_order_id',
        'source_store_event_id',
        'source_store_entity_id',
        'user_id',
        'partner_id',
        'actor_id',
        'output_balance',
        'output_balance_expires_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'confirmed_at',
        'rejected_at',
        'output_balance_expires_at',
    ];

    protected $guarded = [];

    public function beforeSave()
    {
        //@todo move all logic to event listeners
        if (!$this->confirmed_at && (self::STATUS_CONFIRMED === $this->status)) {
            $this->confirmed_at = Carbon::now();
            // force
            $this->rejected_at = null;
        }

        if (!$this->rejected_at && (self::STATUS_REJECTED === $this->status)) {
            $this->rejected_at = Carbon::now();
            // force
            $this->confirmed_at = null;
        }

        if (!$this->id && (self::TYPE_ACTION === $this->type) && $this->action->isExpiringAction()) {
            if (!$this->output_balance_expires_at) {
                $this->output_balance_expires_at = Carbon::now()->addSeconds($this->action->getPointsLifeTime());
            }

            if (!$this->output_balance) {
                $this->output_balance = $this->balance_change;
            }
        }

        if ($this->id && (self::TYPE_ACTION === $this->type) && $this->isDirty('balance_change')) {
            // we adjust output_balance only for expiring transactions
            if ($this->output_balance_expires_at && !$this->isExpired()) {
                $diffChange = $this->balance_change - (float) $this->getOriginal('balance_change');
                $this->output_balance += $diffChange;

                if ($this->output_balance < 0) {
                    $this->output_balance = 0;
                }
            }
        }

        if ($this->action && $this->sourceStoreEvent && Action::TYPE_ORDER_CASHBACK === $this->action->type && $this->sourceStoreEvent->data->predefinedCashback) {
            $this->balance_change = (float) $this->sourceStoreEvent->data->predefinedCashback;
        }

        if ($this->action && $this->sourceStoreEvent && Action::TYPE_ORDER_REFERRAL === $this->action->type && $this->sourceStoreEvent->data->predefinedReferrerCashback) {
            $this->balance_change = (float) $this->sourceStoreEvent->data->predefinedReferrerCashback;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo|Action
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function sourceStoreEvent()
    {
        return $this->belongsTo(StoreEvent::class, 'source_store_event_id');
    }

    public function sourceStoreEntity()
    {
        return $this->belongsTo(StoreEntity::class, 'source_store_entity_id');
    }

    public function findByPromoCode(Partner $partner, $promoCode)
    {
        return
            static::model()
                ->where(\DB::raw("data ->> 'promo_code'"), $promoCode)
                ->where('partner_id', $partner->id)
                ->first();
    }

    public function getAutoConfirmationDatetime()
    {
        return $this->sourceStoreEntity ? ($this->sourceStoreEntity->status_auto_finishes_at) : null;
    }

    public function isPending()
    {
        return Transaction::STATUS_PENDING == $this->status;
    }

    /**
     * @param $data
     *
     * @return TransactionData
     */
    public function getDataAttribute($data)
    {
        return TransactionData::make(\HJson::decode($data));
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = \HJson::encode($value);
    }

    public function getPointsSpent()
    {
        return -1 * $this->balance_change;
    }

    public function isReward()
    {
        return $this->reward_id;
    }

    public function isBitrewardsPayout()
    {
        return $this->isReward() && Reward::TYPE_BITREWARDS_PAYOUT == $this->reward->type;
    }

    public function isBitrewardsRefill()
    {
        return $this->action_id && Action::TYPE_REFILL_BIT === $this->action->type;
    }

    public function isBitrewardsExchangeEthToBit()
    {
        return $this->action_id && Action::TYPE_EXCHANGE_ETH_TO_BIT === $this->action->type;
    }

    public function getBitrewardsSenderAddress()
    {
        if ($this->isBitrewardsPayout() || $this->isBitrewardsRefill() || $this->isBitrewardsExchangeEthToBit()) {
            return $this->data->toArray()[Transaction::DATA_TREASURY_SENDER_ADDRESS] ?? $this->partner->eth_address;
        }

        return null;
    }

    public function getBitrewardsSenderActor()
    {
        if ($address = $this->getBitrewardsSenderAddress()) {
            if ($partner = Partner::model()->where('eth_address', '=', $address)->first()) {
                if ($this->partner_id === $partner->id) {
                    return $partner->title;
                } else {
                    if (($parentTransaction = Transaction::model()
                        ->where('data->'.Transaction::DATA_CHILD_REFILL_BIT_TRANSACTION_ID, '=', $this->id)
                        ->first()) && $user = $parentTransaction->user) {
                        return __("%s's wallet \"%s\"", $user->name ?? $user->email ?? $user->phone, $user->partner->title);
                    } else {
                        return $partner->title;
                    }
                }
            } elseif ($this->isBitrewardsRefill() && $user = User::model()->where('bit_tokens_sender_address', '=', $address)->first()) {
                return __('Your wallet');
            } elseif ($this->isBitrewardsExchangeEthToBit() && $user = User::model()->where('eth_sender_address', '=', $address)->first()) {
                return __('Your wallet');
            }

            return null;
        }

        return null;
    }

    public function getCouponRedeemUrl(): ?string
    {
        return $this->data[Transaction::DATA_URL] ?? null;
    }

    public function getTransferId()
    {
        return $this->data[self::DATA_MAGIC_NUMBER] ?? null;
    }

    public function isPendingRewardExecution()
    {
        return
            self::STATUS_PENDING == $this->status &&
            ($this->data[Transaction::DATA_REWARD_EXECUTION_STARTED_AT] ?? null) &&
            !($this->data[Transaction::DATA_REWARD_EXECUTION_FINISHED_AT] ?? null);
    }

    public function getFiatWithdrawAmount($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_AMOUNT, $default);
    }

    public function getFiatWithdrawFee($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_FEE, $default);
    }

    public function getFiatWithdrawFeeType($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_FEE_TYPE, $default);
    }

    public function getFiatWithdrawFeeValue($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_FEE_VALUE, $default);
    }

    public function getFiatWithdrawCardNumber($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_CARD_NUMBER, $default);
    }

    public function getFiatWithdrawFirstName($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_FIRST_NAME, $default);
    }

    public function getFiatWithdrawLastName($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_LAST_NAME, $default);
    }

    public function getFiatWithdrawExternalOperationId($default = null)
    {
        return $this->getDataValue(self::DATA_FIAT_WITHDRAW_EXTERNAL_OPERATION_ID, $default);
    }

    public function getMerchantBalanceBefore(): ?float
    {
        $balanceBefore = $this->getDataValue(self::DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_BEFORE);

        return null === $balanceBefore ? null : (float) $balanceBefore;
    }

    public function getMerchantBalanceAfter(): ?float
    {
        $balanceAfter = $this->getDataValue(self::DATA_FIAT_WITHDRAW_MERCHANT_BALANCE_AFTER);

        return null === $balanceAfter ? null : (float) $balanceAfter;
    }

    protected function getDataValue(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function isExpired(): bool
    {
        if (!$this->output_balance_expires_at) {
            return false;
        }

        return $this->output_balance_expires_at->isPast();
    }
}
