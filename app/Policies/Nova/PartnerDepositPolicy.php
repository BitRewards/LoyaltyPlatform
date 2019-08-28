<?php

namespace App\Policies\Nova;

use App\Administrator;
use App\Models\PartnerDeposit;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerDepositPolicy
{
    use HandlesAuthorization;

    public function view(Administrator $administrator, PartnerDeposit $partnerDeposit): bool
    {
        return $administrator->partner_id === $partnerDeposit->partner_id;
    }
}
