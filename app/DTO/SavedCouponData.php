<?php

namespace App\DTO;

use App\DTO\PartnerPage\PartnerData;

class SavedCouponData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $statusStr;

    /**
     * @var bool
     */
    public $canBeRedeemed;

    /**
     * @var float
     */
    public $discountAmount;

    /**
     * @var float
     */
    public $discountPercent;

    /**
     * @var string
     */
    public $discountDescription;

    /**
     * @var string
     */
    public $discountFormatted;

    /**
     * @var float
     */
    public $minAmountTotal;

    /**
     * @var string
     */
    public $redeemUrl;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var int
     */
    public $expiredAt;
}
