<?php

namespace App\Models;

/**
 * Class PasswordReset.
 *
 * @property string         $email
 * @property string         $token
 * @property \Carbon\Carbon $created_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PasswordReset whereCreatedAt($value)
 * @mixin \Eloquent
 */
class PasswordReset extends AbstractModel
{
    protected $table = 'password_resets';

    public $timestamps = true;

    protected $fillable = [
        'email',
        'token',
    ];

    protected $guarded = [];
}
