<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HelpItem.
 *
 * @property int                 $id
 * @property int                 $partner_id
 * @property string              $language
 * @property string              $question
 * @property string              $answer
 * @property int                 $position
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \App\Models\Partner $partner
 */
class HelpItem extends Model
{
    use CrudTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'partner_id', 'language', 'question', 'answer', 'position',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'partner_id' => 'integer',
        'position' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
