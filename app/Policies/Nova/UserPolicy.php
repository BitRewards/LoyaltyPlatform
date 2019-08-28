<?php

namespace App\Policies\Nova;

use App\Administrator;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(Administrator $administrator, User $user): bool
    {
        return $administrator->partner_id === $user->partner_id;
    }
}
