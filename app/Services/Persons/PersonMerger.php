<?php
/**
 * PersonMerger.php
 * Creator: lehadnk
 * Date: 07/08/2018.
 */

namespace App\Services\Persons;

use App\Models\Credential;
use App\Models\Person;
use App\Models\User;
use App\Services\UserService;

class PersonMerger
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PersonFinder
     */
    private $personFinder;

    /**
     * @var Credential[]
     */
    public $skippedCredentials = [];

    public function __construct(UserService $userService, PersonFinder $personFinder)
    {
        $this->userService = $userService;
        $this->personFinder = $personFinder;
    }

    public function mergeUserToPerson(Person $person, User $user)
    {
        $samePartnerUser = $person->users()->where('partner_id', '=', $user->partner_id)->first();

        if (!$samePartnerUser) {
            $user->person_id = $person->id;
            $user->save();

            return;
        }

        $this->userService->merge($samePartnerUser, $user, true);
    }

    public function mergePersonToPerson(Person $master, Person $slave)
    {
        $this->skippedCredentials = [];
        $slave->credentials->each(function (Credential $credential) use ($master) {
            if (!$credential->isConfirmed()) {
                $this->skippedCredentials[] = $credential;
                $credential->delete();

                return;
            }

            try {
                $credential->person_id = $master->id;
                $credential->save();
            } catch (\Exception $e) {
                // It looks like the master use has the same credential already, so we should just simply remove it
                $credential->delete();
            }
        });

        $slave->users->each(function ($user) use ($master) {
            $this->mergeUserToPerson($master, $user);
        });
        $slave->delete();
    }
}
