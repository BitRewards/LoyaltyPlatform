<?php

namespace App\DTO\Factory;

use App\DTO\FiatWithdrawTransactionData;
use App\Models\Partner;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class FiatWithdrawTransactionFactory
{
    /**
     * @var \HDate
     */
    protected $dateHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var \HStr
     */
    protected $stringHelper;

    /**
     * @var \HTransaction
     */
    protected $transactionHelper;

    public function __construct(
        \HDate $dateHelper,
        \HAmount $amountHelper,
        \HStr $stringHelper,
        \HTransaction $transactionHelper
    ) {
        $this->dateHelper = $dateHelper;
        $this->amountHelper = $amountHelper;
        $this->stringHelper = $stringHelper;
        $this->transactionHelper = $transactionHelper;
    }

    public function factory(Transaction $transaction): FiatWithdrawTransactionData
    {
        $currency = $transaction->partner->currency;

        $dto = new FiatWithdrawTransactionData();

        $dto->id = $transaction->id;
        $dto->firstName = $transaction->getFiatWithdrawFirstName();
        $dto->lastName = $transaction->getFiatWithdrawLastName();
        $dto->maskedCardNumber = $this->stringHelper::shortCardMask($transaction->getFiatWithdrawCardNumber());
        $dto->amount = $this->amountHelper::fMedium($transaction->getFiatWithdrawFee(0) + $transaction->getFiatWithdrawAmount(0), $currency);
        $dto->fee = $this->amountHelper::fMedium($transaction->getFiatWithdrawFee(0), $currency);
        $dto->sent = $this->amountHelper::fMedium($transaction->getFiatWithdrawAmount(), $currency);
        $dto->feeType = $transaction->getFiatWithdrawFeeType();
        $dto->feeValue = $transaction->getFiatWithdrawFeeValue();
        $dto->feeStr = $this->getFeeStr($transaction);
        $dto->status = $this->transactionHelper::getStatusStr($transaction);
        $dto->createdAtDate = $this->dateHelper::dateFull($transaction->created_at->timestamp);
        $dto->createdAtTime = $this->dateHelper::time($transaction->created_at->timestamp);

        if ($transaction->confirmed_at) {
            $dto->confirmedAtDate = $this->dateHelper::dateFull($transaction->confirmed_at->timestamp);
            $dto->confirmedAtTime = $this->dateHelper::time($transaction->confirmed_at->timestamp);
        }

        return $dto;
    }

    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Transaction $transaction) {
            return $this->factory($transaction);
        });
    }

    protected function getFeeStr(Transaction $transaction): string
    {
        if (Partner::FIAT_WITHDRAW_FEE_TYPE_PERCENT === $transaction->getFiatWithdrawFeeType()) {
            $fee = $transaction->getFiatWithdrawFeeValue();

            return "{$fee}%";
        }

        return '';
    }
}
