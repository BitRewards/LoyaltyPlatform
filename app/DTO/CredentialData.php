<?php

namespace App\DTO;

use App\Services\OAuthService;

class CredentialData extends DTO
{
    public $email;
    public $phone;
    public $name;
    public $password;
    public $email_confirmed_at;
    public $phone_confirmed_at;
    public $signup_type;
    public $referrer_id;
    public $referrer_key;
    public $vk_id;
    public $vk_token;
    public $fb_id;
    public $fb_token;
    public $google_id;
    public $twitter_id;

    public $utm_campaign;
    public $utm_source;
    public $utm_medium;
    public $utm_term;
    public $utm_content;

    public static function createFromSocialNetworkProfile(SocialNetworkProfile $profile): self
    {
        $data = new self();
        $data->name = $profile->name;
        $data->email = $profile->email;
        $data->phone = $profile->phone;
        $data->{self::getSocialNetworkField($profile->socialNetwork)} = $profile->socialNetworkId;

        return $data;
    }

    public static function createFromEmail(string $email): self
    {
        $data = new self();
        $data->email = $email;

        return $data;
    }

    public static function createFromPhone(string $phone): self
    {
        $data = new self();
        $data->phone = $phone;

        return $data;
    }

    public static function getSocialNetworkField(string $socialNetwork)
    {
        switch ($socialNetwork) {
            case OAuthService::VK_SOCIAL_NETWORK:
                return 'vk_id';

            case OAuthService::FB_SOCIAL_NETWORK:
                return 'fb_id';

            case OAuthService::GOOGLE_SOCIAL_NETWORK:
                return 'google_id';

            case OAuthService::TWITTER_SOCIAL_NETWORK:
                return 'twitter_id';

            default:
                return false;
        }
    }

    public function isFromSocialNetwork()
    {
        return null !== $this->vk_id || null !== $this->fb_id || null !== $this->twitter_id || null !== $this->google_id;
    }
}
