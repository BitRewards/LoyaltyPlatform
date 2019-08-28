<?php

namespace App\Models;

use App\DTO\CredentialData;
use Carbon\Carbon;

/**
 * Class Credential.
 *
 * @property $id
 * @property $person_id
 * @property $email
 * @property $email_confirmed_at
 * @property $password
 * @property $phone
 * @property $phone_confirmed_at;
 * @property $vk_id
 * @property $fb_id
 * @property $twitter_id
 * @property $google_id
 * @property $vk_token
 * @property $fb_token
 * @property $type_id
 * @property $title
 * @property $is_confirmed
 * @property Person       $person
 * @property PartnerGroup $partnerGroup
 * @property int partner_group_id
 */
class Credential extends AbstractModel
{
    public const TYPE_EMAIL = 'email';
    public const TYPE_PHONE = 'phone';
    public const TYPE_FACEBOOK = 'facebook';
    public const TYPE_VK = 'vk';
    public const TYPE_TWITTER = 'twitter';
    public const TYPE_GOOGLE = 'google';

    const STATIC_PASSWORD_SALT = 'gah6UThu';

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = \HUser::normalizeEmail($value);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function partnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    public function getTitle(): string
    {
        switch ($this->type_id) {
            case self::TYPE_EMAIL:
                return $this->email;

            case self::TYPE_PHONE:
                return $this->phone;

            case self::TYPE_FACEBOOK:
                return 'https://www.facebook.com/'.$this->fb_id;

            case self::TYPE_VK:
                return 'https://vk.com/id'.$this->vk_id;

            case self::TYPE_TWITTER:
                return 'https://twitter.com/'.$this->twitter_id;

            case self::TYPE_GOOGLE:
                return $this->google_id;

            default:
                return '';
        }
    }

    /**
     * @param CredentialData $credentialData
     * @param Person         $person
     * @param int            $partnerGroupId
     *
     * @return Credential[]
     */
    public static function makeFromUserData(CredentialData $credentialData, Person $person, int $partnerGroupId): array
    {
        $credentials = [];

        if (!empty($credentialData->fb_id)) {
            $credential = new Credential();
            $credential->fb_id = $credentialData->fb_id;
            $credential->fb_token = $credentialData->fb_token;
            $credential->type_id = self::TYPE_FACEBOOK;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        if (!empty($credentialData->vk_id)) {
            $credential = new Credential();
            $credential->vk_id = $credentialData->vk_id;
            $credential->vk_token = $credentialData->vk_token;
            $credential->type_id = self::TYPE_VK;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        if (!empty($credentialData->twitter_id)) {
            $credential = new Credential();
            $credential->twitter_id = $credentialData->twitter_id;
            $credential->type_id = self::TYPE_TWITTER;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        if (!empty($credentialData->google_id)) {
            $credential = new Credential();
            $credential->google_id = $credentialData->google_id;
            $credential->type_id = self::TYPE_GOOGLE;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        if (!empty($credentialData->phone)) {
            $credential = new Credential();
            $credential->phone = $credentialData->phone;
            $credential->phone_confirmed_at = $credentialData->phone_confirmed_at;
            $credential->password = \Hash::make($credentialData->password.self::STATIC_PASSWORD_SALT);
            $credential->type_id = self::TYPE_PHONE;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        if (!empty($credentialData->email)) {
            $credential = new Credential();
            $credential->email = $credentialData->email;
            $credential->email_confirmed_at = $credentialData->email_confirmed_at;
            $credential->password = \Hash::make($credentialData->password.self::STATIC_PASSWORD_SALT);
            $credential->type_id = self::TYPE_EMAIL;
            $credential->person_id = $person->id;
            $credential->partner_group_id = $partnerGroupId;
            $credential->save();
            $credentials[] = $credential;
        }

        return $credentials;
    }

    public function isValid($password)
    {
        if (!$this->is_confirmed) {
            return false;
        }

        if (!in_array($this->type_id, [self::TYPE_EMAIL, self::TYPE_PHONE])) {
            return true;
        }

        return \Hash::check($password.self::STATIC_PASSWORD_SALT, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = \Hash::make($password.self::STATIC_PASSWORD_SALT);
    }

    public function isConfirmed()
    {
        if (in_array($this->type_id, [self::TYPE_FACEBOOK, self::TYPE_GOOGLE, self::TYPE_TWITTER, self::TYPE_VK])) {
            return true;
        }

        return (self::TYPE_EMAIL === $this->type_id && null !== $this->email_confirmed_at) || (self::TYPE_PHONE == $this->type_id && null !== $this->phone_confirmed_at);
    }

    public function getValue()
    {
        switch ($this->type_id) {
            case self::TYPE_EMAIL:
                return $this->email;

            case self::TYPE_PHONE:
                return $this->phone;

            case self::TYPE_FACEBOOK:
                return $this->fb_id;

            case self::TYPE_VK:
                return 'https://vk.com/id'.$this->vk_id;

            case self::TYPE_TWITTER:
                return 'https://twitter.com/'.$this->twitter_id;

            case self::TYPE_GOOGLE:
                return $this->google_id;

            default:
                return '';
        }
    }

    public function confirm()
    {
        if (Credential::TYPE_EMAIL === $this->type_id) {
            $this->email_confirmed_at = Carbon::now();
        }

        if (Credential::TYPE_PHONE === $this->type_id) {
            $this->phone_confirmed_at = Carbon::now();
        }

        $this->is_confirmed = true;
        $this->save();
    }
}
