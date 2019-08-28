<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;

/**
 * Class Token.
 *
 * @property int            $id
 * @property string         $phone
 * @property string         $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SentSms extends AbstractModel
{
    use SaveHooks;

    protected $table = 'sent_sms';

    protected $fillable = [
        'phone',
        'text',
    ];
}
