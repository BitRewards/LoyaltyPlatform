<?php

namespace App\Services;

use App\Models\AuthToken;
use App\Models\User;

class AuthTokenService
{
    /**
     * @var AuthToken
     */
    protected $authTokenModel;

    public function __construct(AuthToken $authTokenModel)
    {
        $this->authTokenModel = $authTokenModel;
    }

    public function createToken(User $user, bool $longLifeToken = false): AuthToken
    {
        $authToken = $this->authTokenModel->newInstance([
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        if (!$longLifeToken) {
            $authToken->renew();
        }

        $authToken->saveOrFail();

        return $authToken;
    }

    public function findAuthToken(string $token): ?AuthToken
    {
        return $this->authTokenModel::where('token', $token)->first();
    }
}
