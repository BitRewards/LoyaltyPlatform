<?php

namespace App\DTO\PartnerStatistic;

class PartnerStatisticData
{
    /**
     * @var bool
     */
    public $isReferralSystemEnabled;

    /**
     * @var int
     */
    public $totalUserCount;

    /**
     * @var int
     */
    public $registrationCount;

    /**
     * @var int
     */
    public $referralPurchaseCount;

    /**
     * @var float
     */
    public $referralPurchaseAmount;

    /**
     * @var float
     */
    public $referralAveragePurchaseAmount;

    /**
     * @var int
     */
    public $loyaltyPurchasesCount;

    /**
     * @var float
     */
    public $loyaltyPurchasesAmount;

    /**
     * @var float
     */
    public $loyaltyAveragePurchaseAmount;

    /**
     * @var int
     */
    public $loyaltyPromoPurchaseCount;

    /**
     * @var float
     */
    public $loyaltyPromoPurchaseAmount;

    /**
     * @var float
     */
    public $loyaltyAveragePromoPurchaseAmount;

    /**
     * @var float
     */
    public $averagePurchaseAmount;

    /**
     * @var array
     */
    public $loyaltyPurchaseTrend;

    /**
     * @var array
     */
    public $referralPurchaseTrend;
}
