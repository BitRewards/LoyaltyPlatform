<?php

namespace App\DTO;

class ActionData extends DTO
{
    /**
     * @var
     */
    public $id;

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
     * @var string
     */
    public $actionReward;

    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var string|null
     */
    public $affiliateUrl;

    /**
     * @var string|null
     */
    public $merchantUrl;

    public function __construct()
    {
        $this->partner = new PartnerData();
    }
}
