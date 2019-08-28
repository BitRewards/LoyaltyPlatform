<?php
/**
 * PersonGenerator.php
 * Creator: lehadnk
 * Date: 05/08/2018.
 */

namespace App\Services\Persons;

use App\DTO\CredentialData;
use App\Models\Person;
use App\Models\User;

class PersonGenerator
{
    /**
     * @var CredentialGenerator
     */
    private $credentialGenerator;

    public function __construct(CredentialGenerator $credentialGenerator)
    {
        $this->credentialGenerator = $credentialGenerator;
    }

    public function createPersonForUser(User $user, int $partnerGroupId): Person
    {
        $person = new Person();
        $user->person_id = $person->id;
        $person->partner_group_id = $partnerGroupId;

        return $person;
    }

    public function makePerson(CredentialData $credentialData, int $partnerGroupId): Person
    {
        $person = new Person();
        $person->partner_group_id = $partnerGroupId;
        $person->save();

        $this->credentialGenerator->generateFromCredentialData($credentialData, $person, $partnerGroupId);

        return $person;
    }
}
