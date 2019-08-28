<?php

namespace App\DTO\PartnerPage;

class RewardViewData
{
    /**
     * @var string
     */
    public $rewardValue;

    /**
     * @var bool
     */
    public $isBigRewardValue;

    /**
     * @var string
     */
    public $rewardMessage;

    /**
     * @var float|int|null
     */
    public $minimalRewardAmount;

    /**
     * @var string
     */
    public $minimalRewardMessage;

    /**
     * @var float
     */
    public $rewardAmount;

    /**
     * @var string
     */
    public $reward;

    /**
     * @var int
     */
    public $pointsLeft;

    /**
     * @var float
     */
    public $fiatLeft;

    /**
     * @var float|int
     */
    public $progressInPercent;

    /**
     * @var string
     */
    public $rewardDiscountMessage;

    public $clientRewardAcquireUrl;
}
