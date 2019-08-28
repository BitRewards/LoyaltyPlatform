<?php

namespace App\Policies;

use App\Administrator;
use Illuminate\Database\Eloquent\Model;

class ActionPolicy extends PartnerEntityPolicy
{
    /**
     * {@inheritdoc}
     */
    public function update(Administrator $user, Model $entity)
    {
        $parent = parent::update($user, $entity);

        return $parent && false === $entity->is_system;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(Administrator $user, Model $entity)
    {
        $parent = parent::destroy($user, $entity);

        return $parent && false === $entity->is_system;
    }
}
