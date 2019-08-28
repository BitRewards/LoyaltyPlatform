<?php

namespace App\Policies\Nova;

use App\Administrator;
use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function view(Administrator $administrator, Transaction $transaction): bool
    {
        return $administrator->partner_id === $transaction->partner_id;
    }
}
