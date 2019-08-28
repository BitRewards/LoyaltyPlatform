<?php

namespace App\DTO;

class RewardData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $price;

    /**
     * @var float
     */
    public $priceAmount;

    /**
     * @var float
     */
    public $priceBitTokens;

    /**
     * @var string
     */
    public $priceBitTokensStr;

    /**
     * @var string
     */
    public $value;

    /**
     * @var float
     */
    public $valueAmount;

    /**
     * @var string
     */
    public $valueType;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $image;

    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var SpecialOfferRewardData
     */
    public $specialOfferReward;

    /**
     * @var string
     */
    public $priceType;

    public function __construct()
    {
        $this->partner = new PartnerData();
    }
}
