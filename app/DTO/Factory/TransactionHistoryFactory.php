<?php

namespace App\DTO\Factory;

use App\DTO\TransactionHistoryData;
use App\Models\Transaction;
use App\Services\Fiat\FiatService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class TransactionHistoryFactory
{
    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var \HTransaction
     */
    protected $transactionHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var \HReward
     */
    protected $rewardHelper;

    /**
     * @var FiatService
     */
    protected $fiatService;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    public function __construct(
        PartnerFactory $partnerFactory,
        \HTransaction $transactionHelper,
        \HAmount $amountHelper,
        \HAction $actionHelper,
        \HReward $rewardHelper,
        FiatService $fiatService,
        UrlGenerator $urlGenerator
    ) {
        $this->partnerFactory = $partnerFactory;
        $this->transactionHelper = $transactionHelper;
        $this->amountHelper = $amountHelper;
        $this->actionHelper = $actionHelper;
        $this->rewardHelper = $rewardHelper;
        $this->fiatService = $fiatService;
        $this->urlGenerator = $urlGenerator;
    }

    public function factory(Transaction $transaction): TransactionHistoryData
    {
        $historyData = new TransactionHistoryData();
        $historyData->id = $transaction->id;
        $historyData->partner = $this->partnerFactory->factory($transaction->partner);
        $historyData->type = $this->getTransactionType($transaction);
        $historyData->status = $transaction->status;
        $historyData->title = $this->transactionHelper::getTitleText($transaction);
        $historyData->image = $this->getTransactionIcon($transaction);
        $historyData->changeBalanceAmount = $transaction->balance_change;
        $historyData->fiatChangeBalanceAmount = $this->fiatService->exchangeBit(
            $transaction->partner->currency,
            $transaction->balance_change
        );
        $historyData->fiatChangeBalanceCurrency = $this->amountHelper::sISO4217($transaction->partner->currency);
        $historyData->changedAt = ($transaction->confirmed_at ?? $transaction->updated_at)->timestamp;
        $historyData->redeemUrl = $transaction->getCouponRedeemUrl();

        return $historyData;
    }

    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Transaction $transaction) {
            return $this->factory($transaction);
        });
    }

    protected function getTransactionType(Transaction $transaction): string
    {
        if ($transaction->action_id) {
            return TransactionHistoryData::TYPE_ACTION;
        }

        if ($transaction->reward_id) {
            return TransactionHistoryData::TYPE_REWARD;
        }

        throw new \DomainException('Unknown transaction type');
    }

    protected function getTransactionIcon(Transaction $transaction): string
    {
        if ($transaction->action) {
            $imagePath = $this->actionHelper::getIconUrl($transaction->action);
        } else {
            $imagePath = $this->rewardHelper::getIconUrl($transaction->reward);
        }

        return $this->urlGenerator->to($imagePath);
    }
}
