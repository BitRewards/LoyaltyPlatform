<?php

namespace App\Models;

use App\Services\Persons\Authenticatable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property User[] users
 * @property Credential[] credentials
 * @property PartnerGroup partnerGroup
 * @property int partner_group_id
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 * @property Carbon|null $last_visited_at
 */
class Person extends AbstractModel implements Authenticatable, PersonInterface
{
    use \Illuminate\Auth\Authenticatable;

    protected $table = 'persons';

    protected $dates = [
        'created_at',
        'updated_at',
        'last_visited_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'person_id');
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    public function partnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    /**
     * Returns the user for specified partner.
     *
     * @param int $partner_id
     *
     * @return User|null
     */
    public function getUser(int $partner_id): ?User
    {
        return $this->users()->where('partner_id', '=', $partner_id)->first();
    }

    public function addCredentials(Credential $credential)
    {
        $credential->person_id = $this->id;
        $credential->partner_group_id = $this->partner_group_id;
        $credential->save();
    }

    public function isEmailConfirmed($email): ?bool
    {
        $credential = $this->credentials
            ->where('type_id', '=', Credential::TYPE_EMAIL)
            ->where('email', '=', $email)
            ->first();

        if (null === $credential) {
            return null;
        }

        return null !== $credential->email_confirmed_at;
    }

    public function isPhoneConfirmed($phone): ?bool
    {
        $credential = $this->credentials
            ->where('type_id', '=', Credential::TYPE_PHONE)
            ->where('phone', '=', $phone)
            ->first();

        if (null === $credential) {
            return null;
        }

        return null !== $credential->phone_confirmed_at;
    }

    public function confirmEmail($email)
    {
        $credential = $this->credentials
            ->where('type_id', '=', Credential::TYPE_EMAIL)
            ->where('email', '=', $email)
            ->first();

        if (!$credential) {
            $credential = new Credential();
            $credential->email = $email;
            $credential->person_id = $this->id;
            $credential->partner_group_id = $this->partner_group_id;
            $credential->type_id = Credential::TYPE_EMAIL;
        }

        $credential->confirm();
        $credential->save();
    }

    public function confirmPhone($phone)
    {
        $credential = $this->credentials
            ->where('type_id', '=', Credential::TYPE_PHONE)
            ->where('phone', '=', $phone)
            ->first();

        if (!$credential) {
            $credential = new Credential();
            $credential->phone = $phone;
            $credential->person_id = $this->id;
            $credential->partner_group_id = $this->partner_group_id;
            $credential->type_id = Credential::TYPE_PHONE;
        }

        $credential->confirm();
    }

    public function hasSocialNetworkId()
    {
        return $this->credentials->whereIn('type_id', [
            Credential::TYPE_FACEBOOK,
            Credential::TYPE_VK,
            Credential::TYPE_TWITTER,
            Credential::TYPE_GOOGLE,
        ])->count() > 0;
    }

    public function getAvailableSocialNetworkNames()
    {
        return $this->credentials->whereIn('type_id', [
            Credential::TYPE_FACEBOOK,
            Credential::TYPE_VK,
            Credential::TYPE_TWITTER,
            Credential::TYPE_GOOGLE,
        ])->map(function (Credential $credential) {
            if ($credential->vk_id) {
                return _('VK');
            } elseif ($credential->fb_id) {
                return _('Facebook');
            } elseif ($credential->google_id) {
                return _('Google');
            } elseif ($credential->twitter_id) {
                return _('Twitter');
            }
        })->implode(', ');
    }

    public function getPersonUsers(): Collection
    {
        return $this->users;
    }

    public function getPrimaryEmail(): ?string
    {
        $emails = $this->credentials
            ->where('type_id', '=', Credential::TYPE_EMAIL)
            ->sortBy('created_at');

        $confirmedEmails = $emails->where('email_confirmed_at', '!=', null);

        if ($confirmedEmails->count() > 0) {
            return $confirmedEmails->first()->email;
        }

        if ($emails->count() > 0) {
            return $emails->first()->email;
        }

        return null;
    }

    public function getPrimaryPhone(): ?string
    {
        $phones = $this->credentials
            ->where('type_id', '=', Credential::TYPE_PHONE)
            ->sortBy('created_at');

        $confirmedPhones = $phones->where('phone_confirmed_at', '!=', null);

        if ($confirmedPhones->count() > 0) {
            return $confirmedPhones->first()->phone;
        }

        if ($phones->count() > 0) {
            return $phones->first()->phone;
        }

        return null;
    }

    public function getName(): ?string
    {
        return $this->users ? $this->users[0]->name : null;
    }

    public function getEmail(): ?string
    {
        return $this->getPrimaryEmail();
    }

    public function getPhone(): ?string
    {
        return $this->getPrimaryPhone();
    }

    public function getPartnerId(): ?int
    {
        return null;
    }

    public function getAuthenticatableType(): string
    {
        return self::TYPE_PERSON;
    }
}
