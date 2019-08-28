<?php

namespace App\DTO;

class WithdrawTransactionData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $transferId;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $recipient;

    /**
     * @var string
     */
    public $payout;

    /**
     * @var float
     */
    public $payoutAmount;

    /**
     * @var string
     */
    public $payoutInPartnerCurrency;

    /**
     * @var string
     */
    public $feeType;

    /**
     * @var string
     */
    public $feeValue;

    /**
     * @var string
     */
    public $fee;

    /**
     * @var float
     */
    public $feeAmount;

    /**
     * @var string
     */
    public $createdAtStr;

    /**
     * @var int
     */
    public $createdAt;
}
