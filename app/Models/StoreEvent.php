<?php

namespace App\Models;

use App\DTO\AdmitadActionData;
use App\DTO\DTO;
use App\DTO\Factory\StoreEntityDataFactory;
use App\DTO\OrderData;
use App\DTO\StoreEntityData;
use App\DTO\StoreEventData;

/**
 * Class StoreEvent.
 *
 * @property int                                         $id
 * @property string                                      $action
 * @property string                                      $entity_type
 * @property string                                      $entity_external_id
 * @property StoreEntityData|OrderData|AdmitadActionData $data
 * @property int                                         $partner_id
 * @property int                                         $store_entity_id
 * @property int                                         $actor_id
 * @property string                                      $processed_at
 * @property Partner                                     $partner
 * @property StoreEntity                                 $entity
 * @property string                                      $converter_type
 * @property array                                       $raw_data
 * @property \Carbon\Carbon                              $created_at
 * @property \Carbon\Carbon                              $updated_at
 * @property \Carbon\Carbon                              $external_event_created_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereEntityType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereEntityExternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereStoreEntityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereProcessedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StoreEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoreEvent extends AbstractModel
{
    protected $table = 'store_events';

    private $converter;

    const ACTION_STORE = 'store';
    const ACTION_CONFIRM = 'confirm';
    const ACTION_REJECT = 'reject';
    const ACTION_SIGNUP = 'signup';
    const ACTION_CUSTOM_BONUS = 'custom-bonus';

    const ACTION_JOIN_FB = 'join-fb';
    const ACTION_JOIN_VK = 'join-vk';

    const ACTION_SHARE_VK = 'share-vk';
    const ACTION_SHARE_FB = 'share-fb';

    const ACTION_REFILL_BIT = 'refill-bit';
    const ACTION_BIT_PAYOUT = 'bit-payout';
    const ACTION_EXCHANGE_ETH_TO_BIT = 'exchange-eth-to-bit';

    const CONVERTER_TYPE_INSALES_ORDER = 'InsalesOrder';
    const CONVERTER_TYPE_SHOPIFY_ORDER = 'ShopifyOrder';
    const CONVERTER_TYPE_EVENTBRITE_ORDER = 'EventbriteOrder';
    const CONVERTER_TYPE_BITRIX_ORDER = 'BitrixOrder';
    const CONVERTER_TYPE_ADMITAD_ACTION = 'AdmitadAction';
    const CONVERTER_TYPE_API_ORDER = 'ApiOrder';

    public $timestamps = true;

    protected $fillable = [
        'action',
        'entity_type',
        'entity_external_id',
        'data',
        'partner_id',
        'store_entity_id',
        'actor_id',
        'processed_at',
        'external_event_created_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'actor_id' => 'integer',
        'external_event_created_at' => 'datetime',
    ];

    protected $guarded = [];

    /**
     * Get action types.
     *
     * @return array
     */
    public static function actions(): array
    {
        return [
            static::ACTION_STORE,
            static::ACTION_CONFIRM,
            static::ACTION_REJECT,
            static::ACTION_SIGNUP,
            static::ACTION_CUSTOM_BONUS,
            static::ACTION_JOIN_FB,
            static::ACTION_JOIN_VK,
            static::ACTION_SHARE_FB,
            static::ACTION_SHARE_VK,
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return StoreEntity|null
     */
    public function entity()
    {
        return $this->belongsTo(StoreEntity::class, 'store_entity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * @return \App\Services\EventDataConverters\Base
     */
    public function getConverter()
    {
        if (!$this->converter_type) {
            return null;
        }

        if (!$this->converter) {
            $className = '\App\Services\EventDataConverters\\'.$this->converter_type;

            $this->converter = new $className($this);
        }

        return $this->converter;
    }

    public function getUncastedAttribute($attribute)
    {
        return $this->getAttributeFromArray($attribute);
    }

    public function getDataAttribute($data): DTO
    {
        return (new StoreEntityDataFactory($this))->factory(\HJson::decode($data));
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = \HJson::encode($value);
    }

    public function getTargetActionId()
    {
        return $this->data[StoreEventData::DATA_KEY_TARGET_ACTION_ID] ?? null;
    }
}
