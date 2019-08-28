<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property int     $partner_id
 * @property Partner $partner
 * @property string  $status
 * @property float   $amount
 * @property float   $fee
 * @property int     $currency
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $confirmed_at
 */
class PartnerDeposit extends AbstractModel
{
    use CrudTrait;

    public const STATUS_CREATED = 'created';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'partner_deposits';

    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
        'currency' => 'int',
        'partner_id' => 'int',
    ];

    public $timestamps = true;

    public $dates = [
        'created_at',
        'updated_at',
        'confirmed_at',
    ];

    protected $fillable = [
        'id' => 'int',
        'partner_id',
        'status',
        'amount',
        'fee',
    ];

    public static function getStatusList(): array
    {
        return [
            self::STATUS_CREATED => __('Created'),
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_CONFIRMED => __('Confirmed'),
            self::STATUS_REJECTED => __('Rejected'),
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
