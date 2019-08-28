<?php

namespace App\DTO\PartnerPage;

use App\Models\Partner;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class TransactionDataFactory
{
    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HTransaction
     */
    protected $transactionHelper;

    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var \HDate
     */
    protected $dateHelper;

    public function __construct(
        TransactionService $transactionService,
        UrlGenerator $urlGenerator,
        \HTransaction $transactionHelper,
        \HAction $actionHelper,
        \HAmount $amountHelper,
        \HDate $dateHelper
    ) {
        $this->transactionService = $transactionService;
        $this->urlGenerator = $urlGenerator;
        $this->transactionHelper = $transactionHelper;
        $this->actionHelper = $actionHelper;
        $this->amountHelper = $amountHelper;
        $this->dateHelper = $dateHelper;
    }

    public function factoryUserTransactions(Partner $partner, User $user): array
    {
        return $this->prepareData($this->transactionService->getUserTransactions($user), $partner, $user);
    }

    public function factoryRewardedUserTransactions(Partner $partner, User $user): array
    {
        return $this->prepareData($this->transactionService->getUserDiscountTransactions($user), $partner, $user);
    }

    public function factoryBitrewardsPayoutTransactions(Partner $partner, User $user): array
    {
        if (!$partner->isBitrewardsEnabled()) {
            return [];
        }

        return $this->prepareData($this->transactionService->getBitrewardsPayoutTransactions($user), $partner, $user);
    }

    public function factoryDepositTransactions(Partner $partner, User $user): array
    {
        if (!$partner->isBitrewardsEnabled()) {
            return [];
        }

        return $this->prepareData($this->transactionService->getDepositTransactions($user), $partner, $user);
    }

    /**
     * @param Collection|Transaction[] $transactions
     * @param Partner                  $partner
     * @param User|null                $user
     *
     * @return TransactionData[]
     */
    protected function prepareData(Collection $transactions, Partner $partner, User $user = null): array
    {
        $result = [];

        foreach ($transactions as $transaction) {
            $transactionData = new TransactionData();
            $transactionData->viewData = $viewData = new TransactionViewData();

            $transactionData->id = $transaction->id;
            $transactionData->type = $transaction->type;
            $transactionData->rewardId = $transaction->reward_id;
            $transactionData->status = $this->transactionHelper::getStatusStr($transaction);
            $transactionData->data = $transaction->data;
            $transactionData->actionId = $transaction->action->id ?? null;
            $transactionData->title = $this->transactionHelper::getTitle($transaction);
            $transactionData->created = $this->dateHelper::dateTime($transaction->created_at);
            $viewData->createdDateFull = $this->dateHelper::dateFull($transaction->created_at);
            $viewData->createdTime = $this->dateHelper::time($transaction->created_at);
            $transactionData->createdAt = $transaction->created_at->timestamp;
            $transactionData->balanceChangeAmount = $transaction->balance_change;
            $transactionData->balanceChangeInPartnerCurrency = $this->amountHelper::getInParentCurrency(
               $transactionData->balanceChangeAmount, $partner
            );
            $transactionData->isBitrewardPayout = $transaction->isBitrewardsPayout();
            $transactionData->isBitrewardsExchangeEthToBit = $transaction->isBitrewardsExchangeEthToBit();
            $transactionData->treasuryEthAmount = $transaction->sourceStoreEvent->data[\App\DTO\StoreEventData::DATA_KEY_TREASURY_ETH_AMOUNT] ?? null;
            $transactionData->isExpiringTransaction = $transaction->action && $transaction->action->isExpiringAction();
            $transactionData->outputBalanceAmount = $transaction->output_balance;
            $transactionData->outputBalance = $this->amountHelper::getInParentCurrency(
                $transaction->output_balance, $partner
            );
            $transactionData->outputBalanceExpiresAt = $transaction->output_balance_expires_at->timestamp ?? null;
            $transactionData->outputBalanceExpiresAtStr = $this->dateHelper::dateFull($transaction->output_balance_expires_at);
            $transactionData->outputBalanceExpiresAtExtraStr = $this->dateHelper::expiringDateFormat($transaction->output_balance_expires_at);
            $transactionData->isExpired = $transaction->output_balance_expires_at && $transaction->output_balance_expires_at->isPast();
            $transactionData->isGradedPercentRewardModeEnabled = $transaction->partner->isGradedPercentRewardModeEnabled();

            $viewData->usageModalUrl = $transactionData->isDataNotUsableByClient()
                ? null
                : $this->urlGenerator->route('client.reward.usageModal', [
                    'partner' => $partner->key,
                    'transaction' => $transaction->id,
                ]);

            $viewData->iconClass = $transaction->action
                ? $this->actionHelper::getIconClass($transaction->action)
                : null;
            $viewData->iconStatusClass = $this->transactionHelper::getStatusIconClass($transaction);
            $viewData->balanceChange = $partner->isFiatReferralEnabled() ? \HAmount::fSign(\HAmount::pointsToFiat($transaction->balance_change, $partner), $partner->currency) : $this->transactionHelper::getBalanceChangeSign($transaction)
                .$this->amountHelper::points($transaction->balance_change);
            $viewData->createDate = $this->dateHelper::dateFull($transaction->created_at);
            $viewData->createTime = $this->dateHelper::time($transaction->created_at->getTimestamp());
            $viewData->payoutAmountInPartnerCurrency = $this->amountHelper::getInParentCurrency(
                $transactionData->payoutAmount(),
                $partner
            );
            $viewData->balanceChangeInPartnerCurrency = $this->amountHelper::getInParentCurrency(
                $transactionData->balanceChangeAmount,
                $partner
            );

            $viewData->bitrewardsSenderActor = $transaction->getBitrewardsSenderActor();
            $viewData->bitrewardsSenderAddress = $transaction->getBitrewardsSenderAddress();

            $result[] = $transactionData;
        }

        return $result;
    }
}
