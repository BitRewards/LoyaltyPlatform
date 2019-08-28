<?php

namespace App\Services;

use App\Models\PartnerDeposit;

class PartnerDepositService
{
    public function getStatus(PartnerDeposit $partnerDeposit): string
    {
        $statusList = PartnerDeposit::getStatusList();

        if (isset($statusList[$partnerDeposit->status])) {
            return $statusList[$partnerDeposit->status];
        }

        throw new \DomainException("Unknown status '{$partnerDeposit->status}'");
    }
}
