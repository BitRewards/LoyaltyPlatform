<?php

namespace App\DTO;

class FiatWithdrawTransactionData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $maskedCardNumber;

    /**
     * @var string
     */
    public $amount;

    /**
     * @var string
     */
    public $fee;

    /**
     * @var string
     */
    public $sent;

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
    public $feeStr;

    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $createdAtDate;

    /**
     * @var string
     */
    public $createdAtTime;

    /**
     * @var string
     */
    public $confirmedAtDate;

    /**
     * @var string
     */
    public $confirmedAtTime;
}
