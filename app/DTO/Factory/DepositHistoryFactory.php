<?php

namespace App\DTO\Factory;

use App\DTO\DepositTransactionData;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class DepositHistoryFactory
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

    public function factory(Transaction $transaction): DepositTransactionData
    {
        $dto = new DepositTransactionData();
        $dto->id = $transaction->id;
        $dto->status = $transaction->status;
        $dto->sender = $transaction->getBitrewardsSenderAddress();
        $dto->deposit = $this->amountHelper::points($transaction->balance_change, $transaction->partner);
        $dto->depositAmount = $transaction->balance_change;
        $dto->payoutInPartnerCurrency = $this->amountHelper::stripCurrencySign(
            $this->amountHelper::getInParentCurrency($transaction->balance_change, $transaction->partner)
        );
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
