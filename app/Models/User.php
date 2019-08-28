<?php

namespace App\Models;

use App\Services\Persons\Authenticatable;
use App\Traits\SelectelImageUploadsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Backpack\CRUD\CrudTrait;

/**
 * @property int                                                                                                       $id
 * @property string                                                                                                    $key
 * @property string                                                                                                    $email
 * @property string                                                                                                    $name
 * @property string                                                                                                    $picture
 * @property float                                                                                                     $balance
 * @property int                                                                                                       $partner_id
 * @property int                                                                                                       $referrer_id
 * @property string                                                                                                    $vk_id
 * @property string                                                                                                    $fb_id
 * @property string                                                                                                    $google_id
 * @property string                                                                                                    $vk_token
 * @property string                                                                                                    $fb_token
 * @property string                                                                                                    $role
 * @property string                                                                                                    $api_token
 * @property int                                                                                                       $giftd_id
 * @property string                                                                                                    $referral_link
 * @property string                                                                                                    $referral_promo_code
 * @property string                                                                                                    $twitter_id
 * @property string                                                                                                    $phone
 * @property string                                                                                                    $signup_type
 * @property bool                                                                                                      $is_unsubscribed
 * @property string                                                                                                    $bit_tokens_sender_address
 * @property string                                                                                                    $eth_sender_address
 * @property int                                                                                                       $person_id
 * @property Person                                                                                                    $person
 * @property string                                                                                                    $phone_normalized
 * @property string                                                                                                    $email_normalized
 * @property string                                                                                                    $password
 * @property Partner                                                                                                   $partner
 * @property User                                                                                                      $referrer
 * @property \Illuminate\Support\Collection|User[]                                                                     $referrals
 * @property Transaction[]                                                                                             $transactions
 * @property Code[]                                                                                                    $codes
 * @property array                                                                                                     $emails_received
 * @property array                                                                                                     $data
 * @property \Carbon\Carbon                                                                                            $email_confirmed_at
 * @property \Carbon\Carbon                                                                                            $phone_confirmed_at
 * @property \Carbon\Carbon                                                                                            $created_at
 * @property \Carbon\Carbon                                                                                            $updated_at
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @property User|null                                                                                                 $lastReferral
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePicture($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePartnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereVkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereFbId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereVkToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereFbToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRole($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereApiKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereGiftdId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmailConfirmedAt($value)
 */
class User extends AbstractModel implements Authenticatable, AuthorizableContract, CanResetPasswordContract, PersonInterface
{
    use \Illuminate\Auth\Authenticatable, Authorizable, CanResetPassword;
    use Notifiable;
    use CrudTrait;
    use SelectelImageUploadsTrait;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PARTNER = 'partner';
    public const ROLE_CASHIER = 'cashier';

    public const SIGNUP_TYPE_ORGANIC = 'organic';
    public const SIGNUP_TYPE_BITREWARDS_APP = 'bitrewards-app';
    public const SIGNUP_TYPE_ORDER = 'order';
    public const SIGNUP_TYPE_GIVE_BONUS = 'give-bonus';
    public const SIGNUP_TYPE_API = 'api';
    public const SIGNUP_TYPE_SAVE_COUPON = 'save-coupon';
    public const SIGNUP_TYPE_UNSECURE_API = 'unsecure-api';

    public const DATA_UTM_SOURCE = 'utm-source';
    public const DATA_UTM_CAMPAIGN = 'utm-campaign';
    public const DATA_UTM_MEDIUM = 'utm-medium';
    public const DATA_UTM_TERM = 'utm-term';
    public const DATA_UTM_CONTENT = 'utm-content';

    protected $table = 'users';

    public $timestamps = true;

    protected $fillable = [
        'key',
        'email',
        'phone',
        'name',
        'picture',
        'balance',
        'partner_id',
        'referrer_id',
        'role',
        'api_token',
        'giftd_id',
        'referral_link',
        'referral_promo_code',
        'signup_type',
        'is_unsubscribed',
        'bit_tokens_sender_address',
        'eth_sender_address',
        'data',
        'person_id',
        'phone_normalized',
    ];

    protected $casts = [
        'balance' => 'float',
        'email_confirmed_at' => 'datetime',
        'phone_confirmed_at' => 'datetime',
        'emails_received' => 'array',
        'data' => 'array',
    ];

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function beforeSave()
    {
        if (!$this->key) {
            $this->key = Str::random(10);
        }

        if (!$this->api_token) {
            $this->api_token = Str::random(16);
        }

        if ($this->email) {
            $this->email = \HUser::normalizeEmail($this->email);
        }

        if ($this->phone) {
            $this->phone = \HUser::normalizePhone($this->phone, $this->partner->default_country);
        }

        if (!$this->email) {
            $this->email = null;
        }

        if (!$this->phone) {
            $this->phone = null;
        }

        if ($this->bit_tokens_sender_address) {
            $this->bit_tokens_sender_address = trim(mb_strtolower($this->bit_tokens_sender_address));
        }

        if ($this->eth_sender_address) {
            $this->eth_sender_address = trim(mb_strtolower($this->eth_sender_address));
        }
    }

    public function findByPartnerAndEmail(Partner $partner, ?string $email): ?User
    {
        if (!$email) {
            return null;
        }

        return $this->whereAttributes([
            'partner_id' => $partner->id,
            'email' => \HUser::normalizeEmail($email),
        ])->first();
    }

    public function findByPartnerAndPhone(Partner $partner, ?string $phone): ?User
    {
        if (!$phone) {
            return null;
        }

        return $this->whereAttributes([
            'partner_id' => $partner->id,
            'phone' => \HUser::normalizePhone($phone, $partner->default_country),
        ])->first();
    }

    /**
     * @param Partner $partner
     * @param $email
     * @param $phone
     *
     * @return User|null
     */
    public function findByPartnerAndEmailOrPhone(Partner $partner, ?string $email, ?string $phone): ?User
    {
        if (Partner::AUTH_METHOD_EMAIL === $partner->getAuthMethod()) {
            return $this->findByPartnerAndEmail($partner, $email) ?? $this->findByPartnerAndPhone($partner, $phone);
        }

        if (Partner::AUTH_METHOD_PHONE === $partner->getAuthMethod()) {
            return $this->findByPartnerAndPhone($partner, $phone) ?? $this->findByPartnerAndEmail($partner, $email);
        }

        return null;
    }

    /**
     * @param $email
     * @param $role
     *
     * @return User|null
     */
    public function findUserByEmailAndRole($email, $role = CanResetPasswordContract::ROLE_PARTNER)
    {
        $result = User::where('email', mb_strtolower($email))->where('role', $role)->first();

        return $result;
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    public function lastReferral()
    {
        return $this
            ->hasOne(User::class, 'referrer_id')
            ->latest();
    }

    public function findByKey($key)
    {
        $result = User::where(['key' => $key])->first();

        return $result;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function isEmptyUser()
    {
        return !$this->email && !$this->phone && !$this->vk_id && !$this->fb_id;
    }

    public function codes()
    {
        return $this->hasMany(Code::class);
    }

    public function getTitle()
    {
        return ($this->name ?: ($this->phone ?: $this->email)) ?: (__('User #%s', $this->id));
    }

    public function isEmailConfirmed(): bool
    {
        return $this->email && $this->person->isEmailConfirmed($this->email);
    }

    public function isPhoneConfirmed(): bool
    {
        return $this->phone && $this->person->isPhoneConfirmed($this->phone);
    }

    public function getLastTransactions($limit = 3)
    {
        return $this->transactions()->limit($limit)->get();
    }

    /**
     * Upload and/or remove old & resize new user picture.
     *
     * @param UploadedFile|null $value
     */
    public function setPictureAttribute($value)
    {
        $attribute = 'picture';
        $disk = 'selectel';
        $path = 'uploads/avatars';

        $this->resizeAndUpload($value, $attribute, $disk, $path, [
            'width' => 200,
            'aspect_ratio' => true,
            'upsize' => true,
        ]);
    }

    public function getBitrewardsTransactions()
    {
        $partner = $this->partner;
        $bitrewardsReward = $partner->getRewardByType(Reward::TYPE_BITREWARDS_PAYOUT);
        $bitRefillAction = $partner->actions()->where('type', '=', Action::TYPE_REFILL_BIT)->first();

        $withdrawTransactions = $bitrewardsReward ? $this->transactions()
            ->where('reward_id', '=', $bitrewardsReward->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)->get() : null;

        $depositTransactions = $bitRefillAction ? $this->transactions()
            ->where('action_id', '=', $bitRefillAction->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)->get() : null;

        return ['withdraw' => $withdrawTransactions ?? [], 'deposit' => $depositTransactions ?? []];
    }

    /**
     * @return Person
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return User[]|Collection
     */
    public function getPersonUsers(): Collection
    {
        return $this->person->users;
    }

    public function getFirstName(): string
    {
        $chunks = explode(' ', $this->name);

        if (count($chunks) > 0) {
            return $chunks[0];
        }

        return '';
    }

    public function getSecondName(): string
    {
        $chunks = explode(' ', $this->name);

        if (count($chunks) > 0) {
            array_shift($chunks);

            return implode(' ', $chunks);
        }

        return '';
    }

    public function getPrimaryEmail(): ?string
    {
        return $this->email ?: null;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('id', 'desc');
    }

    public function getNotifications($limit = 10)
    {
        return $this->notifications()->limit($limit)->get();
    }

    public function getLastTransactionDate()
    {
        return $this->transactions()->orderBy('id', 'DESC')->first()->created_at;
    }

    public function getFiatBalanceAvailableToWithdraw(): float
    {
        return \HAmount::pointsToFiat($this->balance, $this->partner);
    }

    public function hasSomeUtmData()
    {
        return
            ($this->data[self::DATA_UTM_SOURCE] ?? null) ||
            ($this->data[self::DATA_UTM_CAMPAIGN] ?? null) ||
            ($this->data[self::DATA_UTM_CONTENT] ?? null) ||
            ($this->data[self::DATA_UTM_MEDIUM] ?? null) ||
            ($this->data[self::DATA_UTM_TERM] ?? null);
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
        return $this->phone;
    }

    public function getPartnerId(): ?int
    {
        return $this->partner_id;
    }

    public function getAuthenticatableType(): string
    {
        return self::TYPE_USER;
    }

    public function withdraws()
    {
        return $this
            ->hasMany(Transaction::class)
            ->join('rewards', 'rewards.id', 'transactions.reward_id')
            ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW)
            ->orderBy('transactions.id', 'DESC');
    }

    public function storeEntities()
    {
        return $this
            ->hasManyThrough(
                StoreEntity::class,
                Transaction::class,
                null,
                'id',
                null,
                'source_store_entity_id'
            );
    }
}
