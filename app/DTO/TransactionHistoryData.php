<?php

namespace App\DTO;

class TransactionHistoryData extends DTO
{
    const TYPE_ACTION = 'action';
    const TYPE_REWARD = 'reward';

    /**
     * @var int
     */
    public $id;
    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var string - Enum (action|reward)
     */
    public $type;

    /**
     * @var string - Enum (pending|confirmed|rejected)
     */
    public $status;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $image;

    /**
     * @var float
     */
    public $changeBalanceAmount;

    /**
     * @var float
     */
    public $fiatChangeBalanceAmount;

    /**
     * @var string
     */
    public $fiatChangeBalanceCurrency;

    /**
     * @var int
     */
    public $changedAt;

    /**
     * @var string|null - for reward transactions, which are actually coupons
     */
    public $redeemUrl;

    public function __construct()
    {
        $this->partner = new PartnerData();
    }
}
