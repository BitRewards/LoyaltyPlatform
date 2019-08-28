<?php

namespace App\Transformers;

use App\Models\AuthToken;
use League\Fractal\TransformerAbstract;

class AuthTokenTransformer extends TransformerAbstract
{
    public function transform(AuthToken $authToken)
    {
        return [
            'token' => $authToken->token,
            'expired' => $authToken->expired_at->timestamp,
        ];
    }
}
