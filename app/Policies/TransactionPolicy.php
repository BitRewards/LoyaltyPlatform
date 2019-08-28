<?php

namespace App\Policies;

use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the transaction.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Transaction $transaction
     *
     * @return mixed
     */
    public function view($user, Transaction $transaction)
    {
        return $user->id == $transaction->user_id;
    }

    /**
     * Determine whether the user can create transactions.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create($user)
    {
        return $user->can('admin');
    }

    /**
     * Determine whether the user can update the transaction.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Transaction $transaction
     *
     * @return mixed
     */
    public function update($user, Transaction $transaction)
    {
        return $user->id == $transaction->user_id;
    }

    /**
     * Determine whether the user can delete the transaction.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\Transaction $transaction
     *
     * @return mixed
     */
    public function delete($user, Transaction $transaction)
    {
        return $user->id == $transaction->user_id;
    }
}
