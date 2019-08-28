<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $ip
 * @property string $token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $expired_at
 */
class AuthToken extends AbstractModel
{
    use SaveHooks;

    const AUTH_TOKEN_LENGTH = 32;
    const DEFAULT_LIFE_TIME = 3600;

    protected $table = 'auth_tokens';

    public $timestamps = true;

    protected $fillable = [
        'token',
        'user_id',
        'ip',
        'expired_at',
    ];

    /**
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expired_at && (Carbon::now() >= $this->expired_at);
    }

    public function renew()
    {
        $this->expired_at = Carbon::now()->addSeconds(self::DEFAULT_LIFE_TIME);
    }

    public function beforeSave()
    {
        if (!$this->token) {
            $this->token = Str::random(self::AUTH_TOKEN_LENGTH);
        }
    }
}
