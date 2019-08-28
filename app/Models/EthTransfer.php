<?php

namespace App\Models;

use App\DTO\EthTransferData;

/**
 * Class Order.
 *
 * @property int             $id
 * @property string          $from_address
 * @property string          $to_address
 * @property string          $tx_hash
 * @property int             $receiver_user_id
 * @property string          $status
 * @property EthTransferData $data
 * @property User            $receiverUser
 * @property \Carbon\Carbon  $processed_at
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EthTransfer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EthTransfer whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EthTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EthTransfer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EthTransfer extends AbstractModel
{
    const DATA_AMOUNT = 'amount';
    const DATA_EXCHANGE_BIT_AMOUNT = 'exchangeBitAmount';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';

    protected $table = 'eth_transfers';

    public $timestamps = true;

    protected $fillable = [
        'from_address',
        'to_address',
        'tx_hash',
        'receiver_user_id',
        'processed_at',
        'status',
        'data',
    ];

    protected $guarded = [];

    public function getUncastedAttribute($attribute)
    {
        return $this->getAttributeFromArray($attribute);
    }

    /**
     * @param $data
     *
     * @return EthTransferData
     */
    public function getDataAttribute($data)
    {
        return EthTransferData::make(\HJson::decode($data));
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = \HJson::encode($value);
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }
}
