<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use Carbon\Carbon;

/**
 * @property int         $id
 * @property int         $partner_id
 * @property Partner     $partner
 * @property int         $user_id
 * @property User        $user
 * @property string      $status
 * @property string      $code
 * @property float|null  $discount_amount
 * @property float|null  $discount_percent
 * @property string|null $discount_description
 * @property float|null  $min_amount_total
 * @property string      $redeem_url
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|static wherePartnerId(int $partnerId)
 * @method static \Illuminate\Database\Query\Builder|static whereUserId(int $userId)
 * @method static \Illuminate\Database\Query\Builder|static whereCode(string $code)
 */
class SavedCoupon extends AbstractModel
{
    use SaveHooks;

    public const STATUS_NEW = 'new';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_USED = 'used';
    public const STATUS_CANCELED = 'canceled';

    protected $table = 'saved_coupons';
    public $timestamps = true;

    protected $fillable = [
        'partner_id',
        'user_id',
        'code',
        'status',
        'discount_amount',
        'discount_percent',
        'discount_description',
        'min_amount_total',
        'redeem_url',
        'expired_at',
    ];

    protected $casts = [
        'partner_id' => 'int',
        'user_id' => 'int',
        'code' => 'string',
        'status' => 'string',
        'discount_amount' => 'float',
        'discount_percent' => 'float',
        'discount_description' => 'string',
        'min_amount_total' => 'float',
    ];

    protected $dates = [
        'expired_at',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beforeSave(): void
    {
        if (null === $this->status) {
            if ($this->expired_at && $this->expired_at->isPast()) {
                $this->status = self::STATUS_EXPIRED;
            } else {
                $this->status = self::STATUS_NEW;
            }
        }
    }

    public function getDiscountFormatted($useSign = false)
    {
        return
            $this->discount_percent ?
                ((int) $this->discount_percent.'%') :
                ($useSign ?
                    \HAmount::fSign($this->discount_amount, $this->partner->currency) :
                    \HAmount::fShort($this->discount_amount, $this->partner->currency));
    }
}
