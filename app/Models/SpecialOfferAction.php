<?php

namespace App\Models;

use App\Traits\ImageAttributeTrait;
use Backpack\CRUD\CrudTrait;

/**
 * @property int            $id
 * @property string         $image_url
 * @property string         $brand
 * @property int            $action_id
 * @property int            $weight
 * @property Action         $action
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SpecialOfferAction extends AbstractModel implements ImageAttributeInterface
{
    use CrudTrait, ImageAttributeTrait;

    protected $table = 'special_offer_actions';
    public $timestamps = true;

    protected $fillable = [
        'image_url',
        'brand',
        'action_id',
        'weight',
        'image',
    ];

    protected $casts = [
        'image_url' => 'string',
        'brand' => 'string',
        'action_id' => 'int',
        'weight' => 'int',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Action
     */
    public function action()
    {
        return $this->belongsTo(Action::class, 'action_id');
    }

    public function getUploadPath()
    {
        return 'uploads/special-offers/actions/';
    }
}
