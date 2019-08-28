<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 12/11/2018
 * Time: 01:26.
 */

namespace App\Services\Persons;

use App\Models\Credential;
use App\Models\PartnerGroup;
use App\Models\Person;
use Illuminate\Support\Collection;

class PartnerGroupMerger
{
    /**
     * @var Person
     */
    private $currentPerson;

    /**
     * @var Collection
     */
    private $personCredentials;

    /**
     * @var PersonFinder
     */
    private $personFinder;

    /**
     * @var PersonMerger
     */
    private $personMerger;

    /**
     * @var Credential[]
     */
    public $skippedCredentials = [];

    public function __construct(PersonFinder $personFinder, PersonMerger $personMerger)
    {
        $this->personFinder = $personFinder;
        $this->personMerger = $personMerger;
    }

    public function merge(PartnerGroup $master, PartnerGroup $slave)
    {
        Person::where('partner_group_id', '=', $master->id)->each(function (Person $person) use ($slave) {
            $this->currentPerson = $person;
            $this->mergePersonsInto($person, $slave->id);
        });

        Person::where('partner_group_id', '=', $master->id)->each(function (Person $person) use ($master) {
            $person->partner_group_id = $master->id;
            $person->save();
            $person->credentials->each(function (Credential $credential) use ($master) {
                $credential->partner_group_id = $master->id;
                $credential->save();
            });
        });

        foreach ($slave->partners as $partner) {
            $partner->partner_group_id = $master->id;
            $partner->save();
        }
    }

    private function mergePersonsInto(Person $masterPerson, int $slaveGroupId)
    {
        $this->personCredentials = new Collection();

        $this->currentPerson = $masterPerson;

        foreach ($this->currentPerson->credentials as $credential) {
            $this->personCredentials->push($credential);
        }

        echo "Processing person {$this->currentPerson->id}...".PHP_EOL;

        foreach ($this->personCredentials as $masterCredential) {
            if (!$masterCredential->isConfirmed()) {
                continue;
            }
            echo "Found confirmed credential {$masterCredential->type_id} {$masterCredential->getValue()}".PHP_EOL;

            $this
                ->personFinder
                ->getCredential($masterCredential->type_id, $masterCredential->getValue(), $slaveGroupId)
                ->each(function (Credential $matchingCredential) use ($masterCredential) {
                    if ($matchingCredential->person_id === $masterCredential->person_id) {
                        return;
                    }
                    $this->personMerger->mergePersonToPerson($masterCredential->person, $matchingCredential->person);

                    foreach ($this->personMerger->skippedCredentials as $skippedCredential) {
                        $this->skippedCredentials[] = $skippedCredential;
                    }

                    echo "Merging person {$matchingCredential->person->id} into {$masterCredential->person->id}...".PHP_EOL;
                });
        }
    }
}
