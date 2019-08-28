<?php
/**
 * AuthService.php
 * Creator: lehadnk
 * Date: 09/08/2018.
 */

namespace App\Services\Persons;

use App\DTO\CredentialData;
use App\Models\Partner;
use App\Models\Person;
use App\Models\User;

class AuthManagerService
{
    /**
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var PersonGenerator
     */
    private $personGeneratorService;

    /**
     * @var UserGenerator
     */
    private $userGeneratorService;

    public function __construct(
        PersonFinder $personFinderService,
        PersonGenerator $personGeneratorService,
        UserGenerator $userGeneratorService
    ) {
        $this->personFinderService = $personFinderService;
        $this->personGeneratorService = $personGeneratorService;
        $this->userGeneratorService = $userGeneratorService;
    }

    public function getUserByCredentials(CredentialData $credentialData, Partner $partner): ?User
    {
        $credentials = $this->personFinderService->findCredentialByCredentialData($credentialData, $partner->partner_group_id);

        if (null !== $credentials && $credentials->isValid($credentialData->password)) {
            return $credentials->person->getUser($partner->id);
        }

        return null;
    }

    public function getPersonByCredentials(CredentialData $credentialData, Partner $partner): ?Person
    {
        $credentials = $this->personFinderService->findCredentialByCredentialData($credentialData, $partner->partner_group_id);

        if (null !== $credentials && $credentials->isValid($credentialData->password)) {
            return $credentials->person;
        }

        return null;
    }

    public function findOrCreatePersonAndUser(CredentialData $credentialData, Partner $partner, $ignorePasswordCheck = false): User
    {
        $credential = $this->personFinderService->findCredentialByCredentialData($credentialData, $partner->partner_group_id);

        if (!$ignorePasswordCheck && $credential && !$credential->isValid($credentialData->password)) {
            throw new \Exception(__('The user exists, but password is invalid.'));
        }

        $person = null;

        if (!$credential) {
            // no such person found
            $person = $this->personGeneratorService->makePerson($credentialData, $partner->partner_group_id);
        } else {
            $person = $credential->person;
        }

        $user = $person->getUser($partner->id);

        if (!$user) {
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $partner);
        }

        return $user;
    }
}
