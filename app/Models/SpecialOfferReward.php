<?php

namespace App\Models;

use App\Traits\ImageAttributeTrait;
use Backpack\CRUD\CrudTrait;

/**
 * @property int            $id
 * @property string         $image_url
 * @property string         $brand
 * @property int            $reward_id
 * @property int            $weight
 * @property Reward         $reward
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SpecialOfferReward extends AbstractModel implements ImageAttributeInterface
{
    use CrudTrait, ImageAttributeTrait;

    protected $table = 'special_offer_rewards';
    public $timestamps = true;

    protected $fillable = [
        'image_url',
        'brand',
        'reward_id',
        'weight',
        'image',
    ];

    protected $casts = [
        'image_url' => 'string',
        'brand' => 'string',
        'reward_id' => 'int',
        'weight' => 'int',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Reward
     */
    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }

    public function getUploadPath()
    {
        return 'uploads/special-offers/rewards/';
    }
}
