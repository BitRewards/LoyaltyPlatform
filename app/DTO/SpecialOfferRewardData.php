<?php

namespace App\DTO;

class SpecialOfferRewardData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $brand;

    /**
     * @var string
     */
    public $image;

    /**
     * @var RewardData
     */
    public $reward;

    public function __construct()
    {
        $this->reward = new RewardData();
    }
}
