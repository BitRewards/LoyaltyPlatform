<?php

namespace App\Services\Audits;

use OwenIt\Auditing\Contracts\UserResolver as UserResolverContract;

class UserResolver implements UserResolverContract
{
    /**
     * Resolve the ID of the logged User.
     *
     * @return mixed|null
     */
    public static function resolveId()
    {
        return \Auth::check() ? \Auth::user()->getAuthIdentifier() : null;
    }
}
