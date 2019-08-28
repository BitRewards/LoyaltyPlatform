<?php

namespace App\Services;

use App\DTO\StoreEventData;
use App\Models\Action;
use App\Models\Partner;
use App\Models\PersonInterface;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\TransactionOutput;
use App\Models\User;
use App\Services\RewardProcessors\GiftdDiscount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @var DB|\DB
     */
    private $db;

    /**
     * @var RewardService
     */
    private $rewardService;

    /**
     * @var ActionService
     */
    private $actionService;

    public function __construct(DB $db, RewardService $rewardService, ActionService $actionService)
    {
        $this->db = $db;
        $this->rewardService = $rewardService;
        $this->actionService = $actionService;
    }

    public function updateTransactionStatus(Transaction $transaction, string $status): bool
    {
        $this->db::beginTransaction();

        $transaction->status = $status;
        $transaction->save();

        if ($user = $transaction->user) {
            $preventNotification = Transaction::STATUS_REJECTED === $status;
            $user = app(UserService::class)->recalculateBalance($user, $preventNotification);

            $transaction->update([
                'balance_after' => $user->balance,
            ]);
        }

        $this->db::commit();

        return true;
    }

    protected function forceStatus(Transaction $transaction, $status): bool
    {
        if ($transaction->isReward()) {
            // only allow pending rewards to be updated
            if (Transaction::STATUS_CONFIRMED == $transaction->status && Transaction::STATUS_REJECTED == $status) {
                $processor = $transaction->reward->getRewardProcessor();

                if ($processor instanceof GiftdDiscount) {
                    $processor->cancelPromoCode($transaction);
                }
            }

            return $this->updateTransactionStatus($transaction, $status);
        }

        if ($action = $transaction->action) {
            $processor = $action->getActionProcessor();
            // check whether we have entity or not
            if ($processor && $processor->isEntityRequired()) {
                if ($transaction->source_store_entity_id) {
                    if (Transaction::STATUS_CONFIRMED === $status) {
                        $eventData = StoreEventData::makeConfirmation(
                            $transaction->sourceStoreEntity->type,
                            $transaction->sourceStoreEntity->external_id,
                            'forced-by-admin'
                        );
                    } elseif (Transaction::STATUS_REJECTED === $status) {
                        $eventData = StoreEventData::makeRejection(
                            $transaction->sourceStoreEntity->type,
                            $transaction->sourceStoreEntity->external_id,
                            'forced-by-admin'
                        );
                    } else {
                        // strange status here

                        return false;
                    }

                    app(StoreEventService::class)->saveAndHandle($transaction->partner, $eventData);

                    return true;
                }

                return false;
            }

            return $this->updateTransactionStatus($transaction, $status);
        }

        return false;
    }

    public function forceConfirm(Transaction $transaction)
    {
        return $this->forceStatus($transaction, Transaction::STATUS_CONFIRMED);
    }

    public function forceReject(Transaction $transaction): bool
    {
        return \DB::transaction(function () use ($transaction) {
            $result = $this->forceStatus($transaction, Transaction::STATUS_REJECTED);

            if ($result) {
                $this->rejectWriteOffExpiringPoints($transaction);
            }

            return $result;
        });
    }

    public function getUserTransactions(User $user, int $page = 1, int $perPage = 50): Collection
    {
        return $user
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->forPage($page, $perPage)
            ->get();
    }

    public function getPersonTransactions(PersonInterface $person, int $page = 1, int $perPage = 50): Collection
    {
        return Transaction::query()
            ->whereIn('user_id', $person->getPersonUsers()->pluck('id')->toArray())
            ->orderBy('created_at', 'desc')
            ->forPage($page, $perPage)
            ->get();
    }

    public function getUserTransactionsCount(User $user): int
    {
        return $user->transactions()->count();
    }

    public function getPersonTransactionsCount(PersonInterface $person): int
    {
        return Transaction::query()
            ->whereIn('user_id', $person->getPersonUsers()->pluck('id')->toArray())
            ->count();
    }

    protected function discountTransactionQuery(): Builder
    {
        $discountRewards = Reward::whereType(Reward::TYPE_GIFTD_DISCOUNT)->select('id');

        return Transaction::query()
            ->whereIn('reward_id', $discountRewards)
            ->orderBy('created_at', 'desc');
    }

    public function getUserDiscountTransactions(User $user, int $page = 1, int $perPage = 50): Collection
    {
        return $this
            ->discountTransactionQuery()
            ->where('user_id', $user->id)
            ->forPage($page, $perPage)
            ->get();
    }

    public function getUserDiscountTransactionsCount(User $user): int
    {
        return $this
            ->discountTransactionQuery()
            ->where('user_id', $user->id)
            ->count();
    }

    public function getActiveUserDiscountTransactionsCount(User $user): int
    {
        return $this
            ->discountTransactionQuery()
            ->where('user_id', $user->id)
            ->where('status', Transaction::STATUS_PENDING)
            ->count();
    }

    public function getPersonDiscountTransactions(PersonInterface $person, int $page = 1, int $perPage = 50): Collection
    {
        return $this
            ->discountTransactionQuery()
            ->whereIn('user_id', $person->getPersonUsers()->pluck('id')->toArray())
            ->forPage($page, $perPage)
            ->get();
    }

    public function getPersonDiscountTransactionsCount(PersonInterface $person): int
    {
        return $this
            ->discountTransactionQuery()
            ->whereIn('user_id', $person->getPersonUsers()->pluck('id')->toArray())
            ->count();
    }

    public function getUserTransactionDetails(User $user): array
    {
        return $this->db::table('transactions')
            ->select($this->db::raw('action_id, count(*) AS total_count, max(created_at) AS max_created_at'))
            ->where('user_id', $user->id)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->groupBy('action_id')
            ->get()
            ->groupBy('action_id')
            ->toArray();
    }

    public function getBitrewardsPayoutTransactions(User $user, int $page = 1, int $perPage = 50): Collection
    {
        $payoutReward = $this->rewardService->getWithdrawReward($user->partner);

        if (!$payoutReward) {
            return collect([]);
        }

        return $user
            ->transactions()
            ->where('reward_id', $payoutReward->id)
            ->orderBy('created_at', 'desc')
            ->forPage($page, $perPage)
            ->get();
    }

    public function getDepositTransactions(User $user, int $page = 1, int $perPage = 50): Collection
    {
        $queryBuilder = $this->getDepositTransactionsQueryBuilder($user);

        if (!$queryBuilder) {
            return collect([]);
        }

        return $queryBuilder->forPage($page, $perPage)->get();
    }

    public function getDepositTransactionsCount(User $user): ?int
    {
        $queryBuilder = $this->getDepositTransactionsQueryBuilder($user);

        return $queryBuilder ? $queryBuilder->count() : null;
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getDepositTransactionsQueryBuilder(User $user)
    {
        $refillAction = $this->actionService->getRefillAction($user->partner);
        $exchangeEthToBitAction = $this->actionService->getExchangeEthToBitAction($user->partner);

        if (!$refillAction || !$exchangeEthToBitAction) {
            return null;
        }

        return $user
            ->transactions()
            ->where(function ($query) use ($refillAction, $exchangeEthToBitAction) {
                $query->where('action_id', '=', $refillAction->id)
                    ->orWhere('action_id', '=', $exchangeEthToBitAction->id);
            })
            ->orderBy('created_at', 'desc');
    }

    public function getFiatWithdraws(User $user, int $page = 1, $perPage = 15): Collection
    {
        $reward = app(RewardService::class)->getFiatWithdrawReward($user->partner);

        if (!$reward) {
            return new Collection();
        }

        return $user
            ->transactions()
            ->where('reward_id', $reward->id)
            ->forPage($page, $perPage)
            ->get();
    }

    public function getFiatWithdrawsAmount(User $user, \DateTime $from, ?\DateTime $to = null): float
    {
        $reward = app(RewardService::class)->getFiatWithdrawReward($user->partner);

        if (!$reward) {
            return 0;
        }

        /** @var Builder $query */
        $query = $user
            ->transactions()
            ->where('reward_id', $reward->id)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->where('confirmed_at', '>=', $from);

        if ($to) {
            $query->where('confirmed_at', '<', $to);
        }

        return (float) $query->sum('balance_change');
    }

    protected function getActiveExpirationTransactionsBuilder(int $limit = null, int $offset = 0): Builder
    {
        $queryBuilder = Transaction::where('type', Transaction::TYPE_ACTION)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->where('output_balance', '>', 0)
            ->orderBy('output_balance_expires_at');

        if ($limit) {
            $queryBuilder
                ->limit($limit)
                ->offset($offset);
        }

        return $queryBuilder;
    }

    public function getUserActiveExpirationTransactions(User $user): Collection
    {
        return $this
            ->getActiveExpirationTransactionsBuilder()
            ->where('user_id', $user->id)
            ->get();
    }

    public function getRecentlyExpiredTransactions(int $limit = null, int $offset = 0): Collection
    {
        return $this
            ->getActiveExpirationTransactionsBuilder($limit, $offset)
            ->whereNotNull('output_balance_expires_at')
            ->whereRaw('output_balance_expires_at <= NOW()')
            ->get();
    }

    public function cancelExpiringPoints(Transaction $transaction): void
    {
        if (!$transaction->output_balance) {
            return;
        }

        \DB::transaction(function () use ($transaction) {
            $expirationTransaction = new Transaction();
            $expirationTransaction->type = Transaction::TYPE_EXPIRATION;
            $expirationTransaction->status = Transaction::STATUS_CONFIRMED;
            $expirationTransaction->partner_id = $transaction->partner_id;
            $expirationTransaction->user_id = $transaction->user_id;
            $expirationTransaction->balance_change = -1 * $transaction->output_balance;
            $expirationTransaction->saveOrFail();

            $transactionOutput = new TransactionOutput();
            $transactionOutput->status = TransactionOutput::STATUS_CONFIRMED;
            $transactionOutput->transaction_from_id = $transaction->id;
            $transactionOutput->transaction_to_id = $expirationTransaction->id;
            $transactionOutput->amount = $transaction->output_balance;
            $transactionOutput->saveOrFail();

            $transaction->output_balance = 0;
            $transaction->saveOrFail();

            app(UserService::class)->recalculateBalance($transaction->user, true);
        });
    }

    protected function writeOffPoints(Transaction $from, Transaction $to, float $maxAmount): float
    {
        $amount = $from->output_balance < $maxAmount ? $from->output_balance : $maxAmount;

        $transactionOutput = new TransactionOutput();
        $transactionOutput->status = TransactionOutput::STATUS_CONFIRMED;
        $transactionOutput->transaction_from_id = $from->id;
        $transactionOutput->transaction_to_id = $to->id;
        $transactionOutput->amount = $amount;
        $transactionOutput->saveOrFail();

        $from->output_balance -= $amount;
        $from->saveOrFail();

        return $amount;
    }

    public function writeOffExpiringPoints(Transaction $rewardTransaction): void
    {
        $amountLeft = abs($rewardTransaction->balance_change);

        foreach ($this->getUserActiveExpirationTransactions($rewardTransaction->user) as $transaction) {
            $amountLeft -= $this->writeOffPoints($transaction, $rewardTransaction, $amountLeft);

            if ($amountLeft <= 0) {
                break;
            }
        }
    }

    protected function rejectWriteOffExpiringPoints(Transaction $transaction): void
    {
        \DB::beginTransaction();

        $transactionOutputService = app(TransactionOutputService::class);

        $transaction->status = Transaction::STATUS_REJECTED;
        $transaction->saveOrFail();

        switch ($transaction->type) {
            case Transaction::TYPE_ACTION:
                $transactionOutputs = $transactionOutputService
                    ->getConfirmedOutputsByInputTransactions($transaction);

                /** @var TransactionOutput $transactionOutput */
                foreach ($transactionOutputs as $transactionOutput) {
                    $toTransaction = $transactionOutput->toTransaction;

                    if (Transaction::TYPE_REWARD === $toTransaction->type) {
                        $this->writeOffExpiringPoints($toTransaction);
                    }

                    $transactionOutput->status = TransactionOutput::STATUS_REJECTED;
                    $transactionOutput->saveOrFail();
                }

                break;

            case Transaction::TYPE_REWARD:
                $transactionOutputs = $transactionOutputService
                    ->getConfirmedOutputsByOutputTransactions($transaction);

                /** @var TransactionOutput $transactionOutput */
                foreach ($transactionOutputs as $transactionOutput) {
                    $fromTransaction = $transactionOutput->fromTransaction;

                    if (!$fromTransaction->isExpired()) {
                        $fromTransaction->output_balance += $transactionOutput->amount;
                        $transactionOutput->status = TransactionOutput::STATUS_REJECTED;
                        $transactionOutput->save();
                        $fromTransaction->save();
                    }
                }

                break;

            case Transaction::TYPE_EXPIRATION:
                $burnedTransactionOutput = $transactionOutputService->getBurnedTransactionOutput($transaction);
                $burnedTransactionOutput->status = TransactionOutput::STATUS_REJECTED;
                $burnedTransactionOutput->saveOrFail();

                $burnedTransaction = $burnedTransactionOutput->fromTransaction ?? null;

                if ($burnedTransaction) {
                    $burnedTransaction->output_balance += abs($transaction->balance_change);
                    $burnedTransaction->output_balance_expires_at = null;
                    $burnedTransaction->saveOrFail();
                }

                break;
        }

        \DB::commit();
    }

    public function getTransactionsForBurningPointsSummary(User $user): Collection
    {
        return Transaction::where('user_id', $user->id)
            ->where(function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query
                        ->where('output_balance_expires_at', '<=', \DB::raw("NOW() + '14 days'::INTERVAL"))
                        ->where('output_balance', '>', 0);
                });

                $query->orWhere(function (Builder $query) {
                    $query
                        ->where('type', Transaction::TYPE_EXPIRATION)
                        ->where('created_at', '>=', \DB::raw("NOW() - '7 days'::INTERVAL"));
                });
            })
            ->get();
    }

    public function getBurnedPointsAmount(User $user, int $intervalInDays = 14): float
    {
        return abs(Transaction::where('user_id', $user->id)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->where('type', Transaction::TYPE_EXPIRATION)
            ->where('created_at', '>=', \DB::raw("NOW() - '{$intervalInDays} days'::INTERVAL"))
            ->sum('balance_change'));
    }

    protected function getPartnerWithdrawRequests(Partner $partner): ?HasMany
    {
        $reward = app(RewardService::class)->getFiatWithdrawReward($partner);

        if (!$reward) {
            return null;
        }

        return $partner
            ->transactions()
            ->where('status', Transaction::STATUS_PENDING)
            ->where('reward_id', $reward->id);
    }

    public function getPartnerWithdrawRequestsAmount(Partner $partner): float
    {
        $query = $this->getPartnerWithdrawRequests($partner);

        if (!$query) {
            return .0;
        }

        return (float) abs($query->sum('balance_change'));
    }

    public function getPartnerWithdrawRequestsCount(Partner $partner): int
    {
        $query = $this->getPartnerWithdrawRequests($partner);

        if (!$query) {
            return 0;
        }

        return $query->count();
    }

    public function getRequiredAmount(Partner $partner): float
    {
        $sql = <<<SQL
WITH user_balance as (
  SELECT
    user_id,
    SUM(balance_change) as balance
  FROM
    transactions
  WHERE
    (
      (
        action_id IN (SELECT id FROM actions WHERE actions.type IN (:signUp, :orderReferral))
        AND
        status = :confirmed
      )
      OR
      (
        reward_id IN (SELECT id FROM rewards WHERE rewards.type IN (:fiatWithdraw))
        AND
        status IN (:confirmed, :pending)
      )
    )
    AND partner_id = :partnerId
  GROUP BY user_id
)
SELECT COALESCE(sum(balance), 0) as total FROM user_balance WHERE balance >= :minBalance
SQL;

        return (float) \DB::select($sql, [
            'fiatWithdraw' => Reward::TYPE_FIAT_WITHDRAW,
            'signUp' => Action::TYPE_SIGNUP,
            'orderReferral' => Action::TYPE_ORDER_REFERRAL,
            'confirmed' => Transaction::STATUS_CONFIRMED,
            'pending' => Transaction::STATUS_PENDING,
            'partnerId' => $partner->id,
            'minBalance' => $partner->getFiatWithdrawMinAmount(0),
        ])[0]->total;
    }
}
