<?php

namespace App\DTO;

class CouponData extends DTO
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
    public $status;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $comment;

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

    public function __construct()
    {
        $this->partner = new PartnerData();
    }
}
