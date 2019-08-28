<?php

namespace App\DTO;

class ReferralStatisticData extends DTO
{
    /**
     * @var string
     */
    public $totalPurchasesSum;

    /**
     * @var float
     */
    public $totalPurchasesSumAmount;

    /**
     * @var string
     */
    public $averagePurchase;

    /**
     * @var float
     */
    public $averagePurchaseAmount;

    /**
     * @var int
     */
    public $purchasesCount;

    /**
     * @var string
     */
    public $cashBack;

    /**
     * @var float
     */
    public $cashBackAmount;

    /**
     * @var string
     */
    public $cashBackWithdraw;

    /**
     * @var float
     */
    public $cashBackWithdrawAmount;

    /**
     * @var int
     */
    public $uniqueCustomersCount;

    /**
     * @var int
     */
    public $clicksCount;
}
