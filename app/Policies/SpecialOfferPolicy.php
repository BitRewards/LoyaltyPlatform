<?php
/**
 * SpecialOfferPolicy.php
 * Creator: lehadnk
 * Date: 01/08/2018.
 */

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class SpecialOfferPolicy
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
     * @param \App\Models\User $user
     * @param Model            $entity
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('admin');
    }

    /**
     * Determine whether the user can create entity.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('admin');
    }

    /**
     * Determine whether the user can update the entity.
     *
     * @param \App\Models\User $user
     * @param Model            $entity
     *
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('admin');
    }

    /**
     * Determine whether the user can delete the entity.
     *
     * @param \App\Models\User $user
     * @param Model            $entity
     *
     * @return mixed
     */
    public function destroy(User $user)
    {
        return $user->can('admin');
    }
}
