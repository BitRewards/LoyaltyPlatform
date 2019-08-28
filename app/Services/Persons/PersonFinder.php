<?php
/**
 * PersonFinder.php
 * Creator: lehadnk
 * Date: 07/08/2018.
 */

namespace App\Services\Persons;

use App\DTO\CredentialData;
use App\Models\Credential;
use App\Models\CredentialType;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;

class PersonFinder
{
    public function findByEmail(string $email, int $partnerGroupId): ?Person
    {
        return $this->getByCredential(Credential::TYPE_EMAIL, $email, $partnerGroupId);
    }

    public function findByPhone(string $phone, int $partnerGroupId): ?Person
    {
        return $this->getByCredential(Credential::TYPE_PHONE, $phone, $partnerGroupId);
    }

    public function findByEmailOrPhone(string $phoneOrEmail, int $partnerGroupId): ?Person
    {
        $person = $this->findByPhone($phoneOrEmail, $partnerGroupId);

        if ($person) {
            return $person;
        }

        return $this->findByEmail($phoneOrEmail, $partnerGroupId);
    }

    public function getByCredential(string $credentialType, string $value, int $partnerGroupId): ?Person
    {
        $credential = $this->getCredential($credentialType, $value, $partnerGroupId)->first();

        if (null === $credential) {
            return null;
        }

        return $credential->person;
    }

    public function getCredential(string $credentialType, string $value, int $partnerGroupId): ?Builder
    {
        return Credential::where([
            [CredentialType::getField($credentialType), '=', $value],
            ['partner_group_id', '=', $partnerGroupId],
        ]);
    }

    public function findPersonByUserData(CredentialData $credentialData, int $partnerGroupId): ?Person
    {
        $credential = $this->findCredentialByCredentialData($credentialData, $partnerGroupId);

        if (null === $credential) {
            return null;
        }

        return $credential->person;
    }

    public function findByFacebookId(string $fb_id, int $partnerGroupId): ?Person
    {
        return $this->getByCredential(Credential::TYPE_FACEBOOK, $fb_id, $partnerGroupId);
    }

    public function findCredentialByCredentialData(CredentialData $credentialData, int $partnerGroupId): ?Credential
    {
        if (null !== $credentialData->fb_id) {
            $credential = $this->getCredential(Credential::TYPE_FACEBOOK, $credentialData->fb_id, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        if (null !== $credentialData->vk_id) {
            $credential = $this->getCredential(Credential::TYPE_VK, $credentialData->vk_id, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        if (null !== $credentialData->twitter_id) {
            $credential = $this->getCredential(Credential::TYPE_TWITTER, $credentialData->twitter_id, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        if (null !== $credentialData->google_id) {
            $credential = $this->getCredential(Credential::TYPE_GOOGLE, $credentialData->google_id, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        if (null !== $credentialData->email) {
            $credential = $this->getCredential(Credential::TYPE_EMAIL, $credentialData->email, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        if (null !== $credentialData->phone) {
            $credential = $this->getCredential(Credential::TYPE_PHONE, $credentialData->phone, $partnerGroupId)->first();

            if ($credential) {
                return $credential;
            }
        }

        return null;
    }
}
