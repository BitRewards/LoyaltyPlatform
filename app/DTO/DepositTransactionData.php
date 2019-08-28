<?php

namespace App\DTO;

class DepositTransactionData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $sender;

    /**
     * @var string
     */
    public $deposit;

    /**
     * @var float
     */
    public $depositAmount;

    /**
     * @var string
     */
    public $payoutInPartnerCurrency;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var string
     */
    public $createdAtStr;
}
