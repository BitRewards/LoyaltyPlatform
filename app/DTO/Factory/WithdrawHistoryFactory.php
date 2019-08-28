<?php

namespace App\DTO\Factory;

use App\DTO\WithdrawTransactionData;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class WithdrawHistoryFactory
{
    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var \HDate
     */
    protected $dateHelper;

    public function __construct(\HAmount $amountHelper, \HDate $dateHelper)
    {
        $this->amountHelper = $amountHelper;
        $this->dateHelper = $dateHelper;
    }

    public function factory(Transaction $transaction): WithdrawTransactionData
    {
        $partner = $transaction->partner;
        $inPartnerCurrency = $this->amountHelper::getInParentCurrency(
            $transaction->data->bitrewardsPayoutAmount,
            $partner
        );

        $dto = new WithdrawTransactionData();

        $dto->id = $transaction->id;
        $dto->transferId = $transaction->getTransferId();
        $dto->status = $transaction->status;
        $dto->recipient = $transaction->data->ethereumAddress;
        $dto->payout = $this->amountHelper::points($transaction->data->bitrewardsPayoutAmount, $partner);
        $dto->payoutAmount = $transaction->data->bitrewardsPayoutAmount;
        $dto->payoutInPartnerCurrency = $this->amountHelper::stripCurrencySign($inPartnerCurrency);
        $dto->feeType = $transaction->data->bitrewardsWithdrawFeeType;
        $dto->feeValue = $transaction->data->bitrewardsWithdrawFeeValue;
        $dto->fee = $this->amountHelper::points($transaction->data->bitrewardsWithdrawFee, $partner);
        $dto->feeAmount = $transaction->data->bitrewardsWithdrawFee;
        $dto->createdAt = $transaction->created_at->timestamp;
        $dto->createdAtStr = $this->dateHelper::dateTimeFull($transaction->created_at);

        return $dto;
    }

    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Transaction $transaction) {
            return $this->factory($transaction);
        });
    }
}
