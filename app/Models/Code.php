<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;

/**
 * Class Code.
 *
 * @property int            $id
 * @property string         $token
 * @property float          $bonus_points
 * @property int            $user_id
 * @property int            $partner_id
 * @property User           $user
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $acquired_at
 */
class Code extends AbstractModel
{
    use CrudTrait;

    protected $fillable = [
        'token',
        'bonus_points',
        'partner_id',
    ];

    protected $casts = [
        'acquired_at' => 'datetime',
    ];

    protected $table = 'codes';

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Partner
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function beforeSave()
    {
        $this->token = self::normalizeToken($this->token);
    }

    public static function normalizeToken($token)
    {
        return preg_replace('/[^0-9]+/', '', $token);
    }

    public function findByPartnerAndToken(Partner $partner, $token)
    {
        $token = self::normalizeToken($token);

        return Code::model()->whereAttributes(['token' => $token, 'partner_id' => $partner->id])->first();
    }
}
