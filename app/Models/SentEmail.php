<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string   $email
 * @property string   $subject
 * @property string   $body
 * @property string   $token
 * @property int|null $partner_id
 */
class SentEmail extends Model
{
    protected $table = 'sent_emails';

    public $timestamps = true;

    protected $fillable = [
        'email',
        'subject',
        'body',
        'token',
    ];

    protected $guarded = [];
}
