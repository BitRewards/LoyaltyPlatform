<?php

namespace App\Http\Controllers\PublicApi\Traits;

use App\Models\User;

trait SerializesUsers
{
    protected function serializeUser(User $user)
    {
        return [
            'email' => $user->email,
            'phone' => $user->phone,
            'user_key' => $user->key,
            'name' => $user->name,
            'created' => $user->created_at->getTimestamp(),
            'balance' => $user->balance,
        ];
    }
}
