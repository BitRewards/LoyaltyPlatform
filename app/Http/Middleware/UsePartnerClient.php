<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-30
 * Time: 16:07.
 */

namespace App\Http\Middleware;

use App\DTO\CredentialData;
use App\Models\User;
use App\Services\Persons\UserGenerator;

class UsePartnerClient
{
    /**
     * @var UserGenerator
     */
    private $userGenerator;

    public function __construct(UserGenerator $userGenerator)
    {
        $this->userGenerator = $userGenerator;
    }

    public function handle($request, \Closure $next)
    {
        $user = \Auth::user();

        if (!$user instanceof User) {
            return $next($request);
        }

        if (!$request->partner) {
            abort(404);
        }

        if ($request->partner->partner_group_id !== $user->person->partner_group_id) {
            \Auth::logout();

            return $next($request);
        }

        if ($request->partner->id === $user->partner_id) {
            return $next($request);
        }

        $currentPartnerUser = $user->person->getUser($request->partner->id);

        if (!$currentPartnerUser) {
            $credentialData = new CredentialData();
            $credentialData->email = $user->email;
            $credentialData->phone = $user->phone;
            $currentPartnerUser = $this->userGenerator->fromCredentialDataForPerson($credentialData, $user->person, $request->partner);
        }
        \Auth::login($currentPartnerUser);

        return $next($request);
    }
}
