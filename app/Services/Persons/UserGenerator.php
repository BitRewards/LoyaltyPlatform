<?php
/**
 * UserGenerator.php
 * Creator: lehadnk
 * Date: 13/08/2018.
 */

namespace App\Services\Persons;

use App\DTO\CredentialData;
use App\DTO\StoreEventData;
use App\Models\Partner;
use App\Models\Person;
use App\Models\StoreEvent;
use App\Models\User;
use App\Services\StoreEventService;
use App\Services\UserService;
use Illuminate\Contracts\Auth\Authenticatable;

class UserGenerator
{
    const STATIC_PASSWORD_SALT = 'gah6UThu';

    /**
     * @param CredentialData $data
     *
     * @return int
     */
    protected function extractReferrerId(CredentialData $data, Partner $partner): int
    {
        $referrerId = 0;
        $referrer = null;

        // Let's check optional `referrer_id` and `referrer_key` fields of
        // given UserData object. If any of it contains value, save ID
        // or retrieve User by given key and select ID from result.

        if (!is_null($data->referrer_id)) {
            $referrerId = $data->referrer_id;
        } elseif (!is_null($data->referrer_key)) {
            $referrer = User::where('key', $data->referrer_key)->first();
            $referrerId = $referrer->id ?? 0;
        }

        // Next, we'll check if any ID was found, and if not,
        // we'll simply return 0 - this will mean that user
        // was signed up without any referrer data.

        if (0 === $referrerId) {
            return 0;
        }

        // Otherwise check if we're already have loaded
        // Referrer object and get ID from it, or try
        // to load Referrer from DB with saved ID.

        $referrer = $referrer ?? User::find($referrerId);

        if (is_null($referrer)) {
            return 0;
        }

        // Finally, validate that Referrer User and
        // new User are belong to the same Partner.
        // If yes, we've found correct Referrer.

        return $referrer->partner_id === $partner->id ? $referrerId : 0;
    }

    public function fromCredentialDataForPerson(CredentialData $credentialData, Person $person, Partner $partner): User
    {
        \DB::beginTransaction();

        $user = new User();
        $user->person_id = $person->id;
        $user->partner_id = $partner->id;
        $user->balance = 0;
        $user->referrer_id = $this->extractReferrerId($credentialData, $partner) ?: null;

        $userData = (array) $credentialData;

        unset($userData['referrer_id'], $userData['referrer_key']);

        $user->fill($userData);

        if ($user->email) {
            $user->email = mb_strtolower($user->email);
        }

        if ($user->phone) {
            $user->phone = \HUser::normalizePhone($user->phone, $partner->default_country);
        }

        $user->signup_type = $credentialData->signup_type ?: User::SIGNUP_TYPE_ORGANIC;
        $user->save();

        $this->processSystemEvent($user, StoreEvent::ACTION_SIGNUP);

        $user->refresh();

        \DB::commit();

        app(UserService::class)->updateReferralLink($user);

        return $user;
    }

    /**
     * @param User $user
     * @param $action
     * @param array     $additionalData
     * @param User|null $actor
     *
     * @return StoreEvent
     */
    public function processSystemEvent(User $user, $action, $additionalData = [], Authenticatable $actor = null)
    {
        $result = new StoreEventData(
            $action,
            array_merge($additionalData, [
                'userCrmKey' => $user->key,
            ])
        );

        if (!is_null($actor)) {
            $result->actorId = $actor->getAuthIdentifier();
        }

        $service = app(StoreEventService::class);
        $event = $service->saveAndHandle($user->partner, $result);

        return $event;
    }
}
