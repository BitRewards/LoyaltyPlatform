<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 04/11/2018
 * Time: 01:32.
 */

namespace App\Services\Persons;

use App\DTO\CredentialData;
use App\Models\Credential;
use App\Models\Person;

class CredentialGenerator
{
    public function generateFromEmail(Person $person, string $email, int $partnerGroupId): Credential
    {
        $credential = new Credential();
        $credential->type_id = Credential::TYPE_EMAIL;
        $credential->email = $email;
        $credential->partner_group_id = $partnerGroupId;
        $person->addCredentials($credential);

        return $credential;
    }

    public function generateFromPhone(Person $person, string $phone, int $partnerGroupId): Credential
    {
        $credential = new Credential();
        $credential->type_id = Credential::TYPE_PHONE;
        $credential->phone = $phone;
        $credential->partner_group_id = $partnerGroupId;
        $person->addCredentials($credential);

        return $credential;
    }

    public function generateFromCredentialData(CredentialData $credentialData, Person $person, int $partnerGroupId)
    {
        if (!empty($credentialData->vk_id)) {
            if (0 === $person->credentials->where('vk_id', '=', $credentialData->vk_id)->count()) {
                $credential = new Credential();
                $credential->type_id = Credential::TYPE_VK;
                $credential->vk_id = $credentialData->vk_id;
                $credential->vk_token = $credentialData->vk_token;
                $credential->partner_group_id = $partnerGroupId;
                $person->addCredentials($credential);
                $credential->confirm();
            }
        }

        if (!empty($credentialData->fb_id)) {
            if (0 === $person->credentials->where('fb_id', '=', $credentialData->fb_id)->count()) {
                $credential = new Credential();
                $credential->type_id = Credential::TYPE_FACEBOOK;
                $credential->fb_id = $credentialData->fb_id;
                $credential->fb_token = $credentialData->fb_token;
                $credential->partner_group_id = $partnerGroupId;
                $person->addCredentials($credential);
                $credential->confirm();
            }
        }

        if (!empty($credentialData->twitter_id)) {
            if (0 === $person->credentials->where('twitter_id', '=', $credentialData->twitter_id)->count()) {
                $credential = new Credential();
                $credential->type_id = Credential::TYPE_TWITTER;
                $credential->twitter_id = $credentialData->twitter_id;
                $credential->partner_group_id = $partnerGroupId;
                $person->addCredentials($credential);
                $credential->confirm();
            }
        }

        if (!empty($credentialData->google_id)) {
            if (0 === $person->credentials->where('google_id', '=', $credentialData->google_id)->count()) {
                $credential = new Credential();
                $credential->type_id = Credential::TYPE_GOOGLE;
                $credential->google_id = $credentialData->google_id;
                $credential->partner_group_id = $partnerGroupId;
                $person->addCredentials($credential);
                $credential->confirm();
            }
        }

        if (!empty($credentialData->email)) {
            if (0 === $person->credentials->where('email', '=', $credentialData->email)->count()) {
                $credential = $this->generateFromEmail($person, $credentialData->email, $partnerGroupId);

                if (null !== $credentialData->password) {
                    $credential->setPassword($credentialData->password);
                    $credential->save();
                }

                if ($credentialData->isFromSocialNetwork()) {
                    $credential->confirm();
                }
            }
        }

        if (!empty($credentialData->phone)) {
            if (0 === $person->credentials->where('phone', '=', $credentialData->phone)->count()) {
                $credential = new Credential();
                $credential->type_id = Credential::TYPE_PHONE;
                $credential->phone = $credentialData->phone;

                if (null !== $credentialData->password) {
                    $credential->setPassword($credentialData->password);
                }
                $credential->partner_group_id = $partnerGroupId;
                $person->addCredentials($credential);
            }
        }
    }
}
