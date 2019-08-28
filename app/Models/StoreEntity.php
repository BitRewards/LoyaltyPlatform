<?php

namespace App\Models;

use App\DTO\AdmitadActionData;
use App\DTO\DTO;
use App\DTO\Factory\StoreEntityDataFactory;
use App\DTO\OrderData;
use App\DTO\StoreEntityData;
use App\Services\EntityDataProcessors;
use Illuminate\Support\Collection;

/**
 * Class StoreEntity.
 *
 * @property int                                         $id
 * @property string                                      $type
 * @property string                                      $external_id
 * @property \Carbon\Carbon                              $confirmed_at
 * @property \Carbon\Carbon                              $rejected_at
 * @property string                                      $status
 * @property \Carbon\Carbon                              $status_auto_finishes_at
 * @property StoreEntityData|OrderData|AdmitadActionData $data
 * @property int                                         $partner_id
 * @property \Carbon\Carbon                              $created_at
 * @property \Carbon\Carbon                              $updated_at
 * @property Partner                                     $partner
 * @property int                                         $last_processed_store_event_id
 * @property StoreEvent                                  $lastProcessedStoreEvent
 * @property Collection|Transaction[]                    $transactions
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereExternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereConfirmedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoreEntity extends AbstractModel
{
    const TYPE_ORDER = 'Order';
    const TYPE_REVIEW = 'Review';
    const TYPE_SHARE = 'Share';
    const TYPE_JOIN = 'Join';
    const TYPE_TREASURY_WITHDRAWAL = 'TreasuryWithdrawal';
    const TYPE_AFFILIATE_ACTION = 'AffiliateAction';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'store_entities';

    public $timestamps = true;

    private $dataProcessor = null;

    protected $dates = [
        'status_auto_finishes_at',
        'confirmed_at',
    ];

    protected $fillable = [
        'type',
        'external_id',
        'confirmed_at',
        'data',
        'partner_id',
        'status',
        'status_auto_finishes_at',
    ];

    protected $casts = [
    ];

    protected $guarded = [];

    public function isPending()
    {
        return self::STATUS_PENDING == $this->status;
    }

    public function isConfirmed()
    {
        return (bool) $this->confirmed_at;
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'source_store_entity_id');
    }

    /**
     * @return EntityDataProcessors\Base
     */
    public function getDataProcessor()
    {
        if (!$this->dataProcessor) {
            $className = '\App\Services\EntityDataProcessors\\'.$this->type;

            if (class_exists($className)) {
                $this->dataProcessor = new $className($this);
            } else {
                // $this->dataProcessor = new $className($this);
                $this->dataProcessor = new EntityDataProcessors\Base($this);
            }
        }

        return $this->dataProcessor;
    }

    public function getDataAttribute($data): DTO
    {
        return (new StoreEntityDataFactory($this))->factory(\HJson::decode($data));
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = \HJson::encode($value);
    }

    public function lastProcessedStoreEvent()
    {
        return $this->belongsTo(StoreEvent::class);
    }
}
