<?php

namespace App\Services\RewardProcessors;

use App\Administrator;
use App\Exceptions\RewardAcquiringException;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Persons\Authenticatable;
use App\Services\TransactionService;
use App\Services\UserService;

abstract class Base
{
    protected $reward;

    public function __construct(Reward $reward, array $config = [])
    {
        $this->reward = $reward;
    }

    abstract protected function isExecutedOnAcquire(): bool;

    abstract protected function isConfirmedOnExecute(): bool;

    /**
     * Returns transaction data.
     *
     * @param Transaction $transaction
     *
     * @throws RewardAcquiringException
     *
     * @return
     */
    abstract protected function executeRewardInternal(Transaction $transaction): array;

    public function acquire(User $user, $transactionData = [], Authenticatable $actor = null)
    {
        \DB::beginTransaction();

        if ($user->partner_id != $this->reward->partner_id) {
            throw new RewardAcquiringException("User partner_id does not match reward partner_id: {$user->partner_id} != {$this->reward->partner_id}");
        }

        if ($this->reward->price && $user->balance < $this->reward->price) {
            throw new RewardAcquiringException("User {$user->id} has not enough balance to acquire reward {$this->reward->id}");
        }

        if (Reward::STATUS_ENABLED != $this->reward->status) {
            throw new RewardAcquiringException("Trying to acquire non-enabled reward {$this->reward->id}");
        }

        if ($this->reward->isArbitraryPriceAllowed()) {
            $pointsToSpend = $transactionData[Transaction::DATA_POINTS_TO_SPEND] ?? null;

            if (null === $pointsToSpend) {
                $pointsToSpend = $user->balance;
            }
            $points = $pointsToSpend;
        } else {
            $points = \HReward::points($this->reward);
        }

        if ($points && $user->balance < $points) {
            throw new RewardAcquiringException("User {$user->id} has not enough balance to acquire reward {$this->reward->id}");
        }

        $transaction = new Transaction();

        $transaction->type = Transaction::TYPE_REWARD;
        $transaction->balance_change = -1 * $points;
        $transaction->status = Transaction::STATUS_PENDING;
        $transaction->reward_id = $this->reward->id;
        $transaction->user_id = $user->id;
        $transaction->partner_id = $user->partner_id;

        // TODO: split actor_id to 2 nullable columns - actor_user_id and actor_administrator_id
        $transaction->actor_id = (!is_null($actor) ? $actor->getAuthIdentifier() : $user->id);
        $transaction->data = $transactionData;

        $transaction->save();

        app(TransactionService::class)->writeOffExpiringPoints($transaction);

        $balanceBefore = $user->balance;
        $user = app(UserService::class)->recalculateBalance($user);

        $transaction->update([
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
        ]);

        \DB::commit();

        if ($this->isExecutedOnAcquire()) {
            $this->executeReward($transaction);
        }

        return $transaction;
    }

    public function executeReward(Transaction $transaction, bool $recalculateBalance = false)
    {
        while (\DB::transactionLevel()) {
            \DB::commit();
        }

        if (Transaction::STATUS_PENDING != $transaction->status) {
            throw new \RuntimeException("Unable to executeReward for transaction {$transaction->id} with status {$transaction->status}");
        }

        if ($transaction->isPendingRewardExecution()) {
            throw new \RuntimeException("Unable to executeReward for transaction {$transaction->id}, because it's in pending reward execution state");
        }

        $transaction->data[Transaction::DATA_REWARD_EXECUTION_STARTED_AT] = microtime(true);
        $transaction->save();

        try {
            $transaction->data = array_replace_recursive(
                (array) $transaction->data,
                $this->executeRewardInternal($transaction)
            );

            if ($this->isConfirmedOnExecute()) {
                $transaction->status = Transaction::STATUS_CONFIRMED;
            }
            $transaction->data[Transaction::DATA_REWARD_EXECUTION_FINISHED_AT] = microtime(true);
            $transaction->save();

            if ($recalculateBalance) {
                app(UserService::class)->recalculateBalance($transaction->user, true);
            }
        } catch (RewardAcquiringException $e) {
            while (\DB::transactionLevel()) {
                \DB::commit();
            }

            app(TransactionService::class)->updateTransactionStatus($transaction, Transaction::STATUS_REJECTED);

            $transaction->data[Transaction::DATA_REWARD_EXECUTION_FINISHED_AT] = microtime(true);
            $transaction->save();

            throw $e;
        }
    }

    public function getDiscountMinAmountTotal()
    {
        return $this->reward->config[Reward::CONFIG_MIN_AMOUNT_TOTAL] ?? null;
    }

    public function getDiscountAmount($amountTotal)
    {
        if ($minTotal = $this->getDiscountMinAmountTotal()) {
            if ($minTotal > $amountTotal) {
                return 0;
            }
        }

        if (Reward::VALUE_TYPE_PERCENT == $this->reward->value_type) {
            return round(($this->reward->value / 100) * $amountTotal, 2);
        }

        if (Reward::VALUE_TYPE_FIXED == $this->reward->value_type) {
            return $this->reward->value;
        }

        return 0;
    }

    public function getDiscountPercent($amountTotal)
    {
        if (!$amountTotal) {
            if (Reward::VALUE_TYPE_PERCENT == $this->reward->value_type) {
                return (float) $this->reward->value;
            }

            if ($minTotal = $this->getDiscountMinAmountTotal()) {
                return round(($this->reward->value / $minTotal) * 100);
            }

            return 0;
        } else {
            $discount = $this->getDiscountAmount($amountTotal);

            return round(($discount / $amountTotal) * 100);
        }
    }

    public function getConfig($key, $default = null)
    {
        return $this->reward->config[$key] ?? $default;
    }
}
