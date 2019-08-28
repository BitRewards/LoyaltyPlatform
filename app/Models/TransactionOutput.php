<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int                 $id
 * @property int                 $transaction_from_id
 * @property int                 $transaction_to_id
 * @property float               $amount
 * @property string              $status
 * @property Transaction         $fromTransaction
 * @property Transaction         $toTransaction
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $confirmed_at
 * @property \Carbon\Carbon|null $rejected_at
 */
class TransactionOutput extends AbstractModel
{
    use SaveHooks;

    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'transaction_outputs';

    protected $casts = [
        'amount' => 'float',
    ];

    protected $fillable = [
        'transaction_from_id',
        'transaction_to_id',
        'amount',
    ];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function beforeSave(): void
    {
        if ((self::STATUS_CONFIRMED === $this->status) && !$this->confirmed_at) {
            $this->confirmed_at = Carbon::now();
        } elseif ((self::STATUS_REJECTED === $this->status) && !$this->rejected_at) {
            $this->rejected_at = Carbon::now();
        }
    }

    /**
     * @return HasOne|Transaction
     */
    public function fromTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_from_id');
    }

    /**
     * @return HasOne|Transaction
     */
    public function toTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_to_id');
    }
}
