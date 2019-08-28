<?php

namespace App\DTO;

class PartnerWalletData extends DTO
{
    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var float
     */
    public $balanceAmount;

    /**
     * @var float
     */
    public $fiatAmount;

    /**
     * @var int
     */
    public $fiatCurrency;

    /**
     * @var int
     */
    public $couponsCount;

    public function __construct()
    {
        $this->partner = new PartnerData();
    }
}
