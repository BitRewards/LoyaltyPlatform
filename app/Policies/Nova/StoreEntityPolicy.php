<?php

namespace App\Policies\Nova;

use App\Administrator;
use App\Models\StoreEntity;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoreEntityPolicy
{
    use HandlesAuthorization;

    public function view(Administrator $administrator, StoreEntity $storeEntity): bool
    {
        return $administrator->partner_id === $storeEntity->partner_id;
    }
}
