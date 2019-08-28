<?php

namespace App\Policies;

use App\Administrator;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class PartnerEntityPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->can('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the entity.
     *
     * @param \App\User|\App\Administrator $user
     * @param Model                        $entity
     *
     * @return mixed
     */
    public function view($user, Model $entity)
    {
        return $user->partner_id == $entity->partner_id;
    }

    /**
     * Determine whether the user can create entity.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(Administrator $user)
    {
        return $user->can('partner');
    }

    /**
     * Determine whether the user can update the entity.
     *
     * @param \App\User $user
     * @param Model     $entity
     *
     * @return mixed
     */
    public function update(Administrator $user, Model $entity)
    {
        return $user->can('partner') && $user->partner_id == $entity->partner_id;
    }

    /**
     * Determine whether the user can delete the entity.
     *
     * @param \App\User $user
     * @param Model     $entity
     *
     * @return mixed
     */
    public function destroy(Administrator $user, Model $entity)
    {
        return $user->can('partner') && ($user->partner_id == $entity->partner_id);
    }
}
