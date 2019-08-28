<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class ReferrerBalanceData extends DTO
{
    /**
     * @var string
     */
    public $earned;

    /**
     * @var float
     */
    public $earnedAmount;

    /**
     * @var string
     */
    public $currentBalance;

    /**
     * @var float
     */
    public $currentBalanceAmount;

    /**
     * @var string
     */
    public $blocked;

    /**
     * @var float
     */
    public $blockedAmount;

    /**
     * @var string
     */
    public $paid;

    /**
     * @var float
     */
    public $paidAmount;

    /**
     * @var string
     */
    public $availableForWithdraw;

    /**
     * @var float
     */
    public $availableForWithdrawAmount;

    /**
     * @var Collection|FiatWithdrawTransactionData[]
     */
    public $withdrawTransactions;

    public function __construct()
    {
        $this->withdrawTransactions = new Collection();
    }
}
