<?php

namespace App\DTO\PartnerPage;

use App\Models\Transaction;

class TransactionData
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
     * @var int
     */
    public $rewardId;

    /**
     * @var string
     */
    public $status;

    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    public $actionId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $created;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var float
     */
    public $balanceChangeAmount;

    /**
     * @var string
     */
    public $balanceChangeInPartnerCurrency;

    /**
     * @var bool
     */
    public $isBitrewardPayout;

    /**
     * @var float
     */
    public $payoutAmount;

    /**
     * @var float
     */
    public $withdrawFee;

    /**
     * @var TransactionViewData
     */
    public $viewData;

    /**
     * @var bool
     */
    public $isBitrewardsExchangeEthToBit;

    /**
     * @var float|null
     */
    public $treasuryEthAmount;

    /**
     * @var bool
     */
    public $isExpiringTransaction;

    /**
     * @var float
     */
    public $outputBalanceAmount;

    /**
     * @var string
     */
    public $outputBalance;

    /**
     * @var int
     */
    public $outputBalanceExpiresAt;

    /**
     * @var string
     */
    public $outputBalanceExpiresAtStr;

    /**
     * @var string
     */
    public $outputBalanceExpiresAtExtraStr;

    /**
     * @var bool
     */
    public $isExpired;

    /**
     * @var bool
     */
    public $isGradedPercentRewardModeEnabled;

    public function isDataNotUsableByClient(): bool
    {
        return $this->data[Transaction::DATA_NOT_USABLE_BY_CLIENT] ?? false;
    }

    public function bitRewardsPayoutAmount()
    {
        return $this->data[Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT] ?? null;
    }

    public function ethereumAddress(): string
    {
        return $this->data[Transaction::DATA_ETHEREUM_ADDRESS] ?? '';
    }

    public function payoutAmount()
    {
        return $this->data[Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT] ?? 0;
    }

    public function magicNumber()
    {
        return $this->data[Transaction::DATA_MAGIC_NUMBER] ?? '';
    }

    public function withdrawFee()
    {
        return $this->data[Transaction::DATA_BITREWARDS_WITHDRAW_FEE] ?? 0;
    }
}
