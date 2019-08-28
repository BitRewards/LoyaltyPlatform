<?php

namespace App\Services;

use App\Models\Token;
use App\Models\User;

class TokenService
{
    public function create(User $owner, string $type, string $destinationType = null): Token
    {
        $token = new Token([
            'owner_user_id' => $owner->id,
            'type' => $type,
            'destination_type' => $destinationType,
        ]);

        switch ($destinationType) {
            case Token::DESTINATION_TYPE_EMAIL:
                $destination = $owner->email;

                break;

            case Token::DESTINATION_TYPE_PHONE:
                $destination = $owner->phone;

                break;

            default:
                $destination = null;
        }

        $token->destination = $destination;

        if (!$token->generateTokenAndSave()) {
            throw new \RuntimeException('Generate token failed');
        }

        return $token;
    }

    public function createAutoLoginTokenForEmail(User $user): Token
    {
        if (!$user->email) {
            throw new \InvalidArgumentException('User email not defined');
        }

        return $this->create($user, Token::TYPE_AUTO_LOGIN, Token::DESTINATION_TYPE_EMAIL);
    }

    public function createAutoLoginTokenForMobilePhone(User $user): Token
    {
        if (!$user->phone) {
            throw new \InvalidArgumentException('User phone number not defined');
        }

        return $this->create($user, Token::TYPE_AUTO_LOGIN, Token::DESTINATION_TYPE_PHONE);
    }

    public function createAutoLoginTokenForWeb(User $user): Token
    {
        return $this->create($user, Token::TYPE_AUTO_LOGIN, Token::DESTINATION_TYPE_WEB);
    }
}
