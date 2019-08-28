<?php

namespace App;

use App\Db\Traits\SaveHooks;
use App\Models\Partner;
use Backpack\CRUD\CrudTrait;
use App\Services\Persons\Authenticatable;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string      $remember_token
 * @property string      $role
 * @property string      $api_token
 * @property int         $partner_id
 * @property Partner     $partner
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $last_visited_at
 * @property bool        $is_main
 */
class Administrator extends Model implements Authenticatable, Authorizable, CanResetPassword
{
    use \Illuminate\Auth\Authenticatable;
    use \Illuminate\Foundation\Auth\Access\Authorizable;
    use CrudTrait;
    use SaveHooks;
    use \Illuminate\Auth\Passwords\CanResetPassword;
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PARTNER = 'partner';
    public const ROLE_CASHIER = 'cashier';

    const STATIC_PASSWORD_SALT = 'gah6UThu';

    protected $dates = [
        'created_at',
        'updated_at',
        'last_visited_at',
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = mb_strtolower($value);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function beforeSave()
    {
        if (!$this->api_token) {
            $this->api_token = str_random(16);
        }
    }

    public function setPassword(string $password)
    {
        $this->password = \Hash::make($password.self::STATIC_PASSWORD_SALT);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return null;
    }

    public function getPartnerId(): ?int
    {
        return $this->partner_id;
    }

    public function getAuthenticatableType(): string
    {
        return self::TYPE_ADMINISTRATOR;
    }

    public function isPartner(): bool
    {
        return self::ROLE_PARTNER === $this->role;
    }

    public function isAdmin(): bool
    {
        return self::ROLE_ADMIN === $this->role;
    }

    public function getIsMainAttribute($value)
    {
        return (bool) $value;
    }

    public function save(array $options = [])
    {
        \DB::transaction(function () use ($options) {
            if (!$this->api_token) {
                $this->api_token = str_random(16);
            }

            if (\array_key_exists('is_main', $this->getDirty())) {
                Administrator::query()
                    ->where('partner_id', $this->partner_id)
                    ->where('is_main', $this->is_main)
                    ->update(['is_main' => null]);

                if (!$this->is_main) {
                    $this->is_main = null;
                }
            }

            parent::save($options);
        });
    }
}
