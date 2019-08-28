<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use Backpack\CRUD\CrudTrait;

/**
 * Class Partner.
 *
 * @property int            $id
 * @property string         $message
 * @property string         $level_name
 * @property mixed          $extra
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $giftd_api_key
 */
class Log extends AbstractModel
{
    use CrudTrait;
    use SaveHooks;

    protected $fillable = [
        'level_name',
        'message',
    ];

    protected $table = 'log';

    public $timestamps = true;
}
