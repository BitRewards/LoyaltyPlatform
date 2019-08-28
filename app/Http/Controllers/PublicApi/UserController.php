<?php

namespace App\Http\Controllers\PublicApi;

use App\DTO\CustomBonusData;
use App\DTO\CredentialData;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicApi\Traits\SerializesUsers;
use App\Http\Requests\PublicApi\User\GiveBonus;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    use SerializesUsers;

    private function createUserByBonusRequest(GiveBonus $bonus)
    {
        $data = CredentialData::make([
            'email' => $bonus->email,
            'phone' => $bonus->phone,
            'name' => $bonus->name,
            'password' => null,
            'signup_type' => User::SIGNUP_TYPE_GIVE_BONUS,
        ]);

        return app(UserService::class)->createClient($data, $bonus->user()->partner);
    }

    public function giveBonus(GiveBonus $request)
    {
        $user = $request->getBonusReceiver();

        if (!$user) {
            if ($request->auto_create_user) {
                $user = $this->createUserByBonusRequest($request);
            } else {
                throw new \RuntimeException('Something strange: validation passed auto_create_user = 0 and empty user!');
            }
        }

        $points = (int) $request->points;

        app(UserService::class)->giveCustomBonusToUser(
            new CustomBonusData($user, $points, \Auth::user())
        );

        return jsonResponse($this->serializeUser($user));
    }
}
