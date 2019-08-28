<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/16/18
 * Time: 12:30 AM.
 */

namespace App\Services\Persons;

use App\Models\Credential;
use App\Models\Person;
use Illuminate\Support\Collection;

/**
 * Class PersonRecursiveMerger.
 */
class PersonRecursiveMerger
{
    /**
     * @var int
     */
    private $currentPersonId = 1;

    /**
     * @var int
     */
    private $lastPersonId;

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
        $firstPerson = Person::orderBy('id', 'desc')->limit(1)->first();

        if (!$firstPerson) {
            return;
        }

        $this->lastPersonId = $firstPerson->id;
        $this->personFinder = $personFinder;
        $this->personMerger = $personMerger;
    }

    public function run()
    {
        while ($this->currentPersonId <= $this->lastPersonId) {
            $this->mergePersonsInto($this->currentPersonId);
            ++$this->currentPersonId;
        }
    }

    public function mergePersonsInto(int $id)
    {
        $this->personCredentials = new Collection();

        $this->currentPerson = Person::find($id);

        if (!$this->currentPerson) {
            return;
        }

        foreach ($this->currentPerson->credentials as $credential) {
            $this->personCredentials->push($credential);
        }

        echo "Processing person $id...".PHP_EOL;

        foreach ($this->personCredentials as $masterCredential) {
            if (!$masterCredential->isConfirmed()) {
                continue;
            }
            echo "Found confirmed credential {$masterCredential->type_id} {$masterCredential->getValue()}".PHP_EOL;

            $this
                ->personFinder
                ->getCredential($masterCredential->type_id, $masterCredential->getValue())
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
