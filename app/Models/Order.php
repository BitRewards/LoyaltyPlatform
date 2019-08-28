<?php

namespace App\Models;

/**
 * Class Order.
 *
 * @property int            $id
 * @property string         $external_id
 * @property float          $amount_total
 * @property string         $email
 * @property string         $promo_code
 * @property int            $referred_by_user_id
 * @property int            $user_id
 * @property string         $status
 * @property \Carbon\Carbon $confirmed_at
 * @property int            $partner_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereExternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereAmountTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order wherePromoCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereReferredByUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereConfirmationTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereConfirmedAt($value)
 */
class Order extends AbstractModel
{
    protected $table = 'orders';

    public $timestamps = true;

    protected $fillable = [
        'external_id',
        'amount_total',
        'email',
        'promo_code',
        'referred_by_user_id',
        'user_id',
        'status',
        'confirmed_at',
        'partner_id',
    ];

    protected $guarded = [];
}
