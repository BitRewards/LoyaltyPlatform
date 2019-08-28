<?php

namespace App\DTO\PartnerStatistic;

class LoyaltyPurchaseStatisticData
{
    /**
     * @var int
     */
    public $purchaseCount = 0;

    /**
     * @var float
     */
    public $purchaseTotalAmount = 0.;

    /**
     * @var float
     */
    public $averagePurchaseAmount = 0.;

    /**
     * @var int
     */
    public $promoPurchaseCount = 0;

    /**
     * @var float
     */
    public $promoPurchaseAmount = 0.;

    /**
     * @var float
     */
    public $averagePromoPurchaseAmount = 0.;
}
