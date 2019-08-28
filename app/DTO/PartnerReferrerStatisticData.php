<?php

namespace App\DTO;

class PartnerReferrerStatisticData extends DTO
{
    /**
     * @var array
     */
    public $activeReferrersCountDailyStatistic = [];

    /**
     * @var int
     */
    public $activeReferrersTotalCount = 0;

    /**
     * @var array
     */
    public $linkClicksDailyStatistic = [];

    /**
     * @var int
     */
    public $totalLinkClicks = 0;

    /**
     * @var array
     */
    public $purchasesAmountDailyStatistic = [];

    /**
     * @var float
     */
    public $totalPurchasesAmount = .0;

    /**
     * @var array
     */
    public $purchasesCountDailyStatistic = [];

    /**
     * @var int
     */
    public $totalPurchasesCount = 0;

    /**
     * @var array
     */
    public $buyersCountDailyStatistic = [];

    /**
     * @var int
     */
    public $buyersTotalCount = 0;

    /**
     * @var array
     */
    public $uniqueReferralsCountDailyStatistic = [];

    /**
     * @var int
     */
    public $uniqueReferralsTotalCount = 0;
}
