<?php

namespace App\DTO\Factory;

use App\DTO\CouponData;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class CouponFactory
{
    /**
     * @var \HTransaction
     */
    protected $transactionHelper;

    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    public function __construct(\HTransaction $transactionHelper, PartnerFactory $partnerFactory)
    {
        $this->transactionHelper = $transactionHelper;
        $this->partnerFactory = $partnerFactory;
    }

    public function factory(Transaction $transaction): CouponData
    {
        return CouponData::make([
            'id' => $transaction->id,
            'partner' => $this->partnerFactory->factory($transaction->partner),
            'status' => $transaction->status,
            'title' => $this->transactionHelper::getTitleText($transaction),
            'comment' => $transaction->comment,
            'redeemUrl' => $transaction->getCouponRedeemUrl(),
            'createdAt' => $transaction->created_at->timestamp,
            'expiredAt' => $transaction->data['expires'] ?? null,
        ]);
    }

    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Transaction $transaction) {
            return $this->factory($transaction);
        });
    }
}
