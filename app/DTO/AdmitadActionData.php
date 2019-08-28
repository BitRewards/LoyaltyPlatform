<?php

namespace App\DTO;

class AdmitadActionData extends StoreEntityData
{
    /**
     * @var bool
     */
    public $isAffiliateRewardsPaidToUs;

    /**
     * @var string
     */
    public $originalOrderId;

    /**
     * @var float
     */
    public $affiliateRewardAmount;

    /**
     * @var int
     */
    public $crmActionId;
}
