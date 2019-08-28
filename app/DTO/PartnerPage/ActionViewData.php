<?php

namespace App\DTO\PartnerPage;

class ActionViewData
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $canBeDone;

    /**
     * @var string
     */
    public $impossibleReason;

    /**
     * @var string
     */
    public $rewardAmount;

    /**
     * @var string
     */
    public $iconClass;

    /**
     * @var string
     */
    public $clientEventProcessUrl;

    /**
     * @var int
     */
    public $groupId;

    /**
     * @var string
     */
    public $shareUrl;

    /**
     * @var string
     */
    public $pageUrl;

    /**
     * @var string[]
     */
    public $valuePolicyRules;
}
