<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PartnerService;
use Illuminate\Support\Collection;

class BurningPointsSummary extends UserNotification
{
    public const STATISTIC_INTERVAL_IN_DAYS = 14;

    /**
     * @var Collection
     */
    protected $transactions;

    public function __construct(User $user, Collection $transactions)
    {
        parent::__construct($user);

        $this->transactions = $transactions;
    }

    protected function getBurnedTransactions(): Collection
    {
        return $this->transactions->filter(function (Transaction $transaction) {
            return Transaction::TYPE_EXPIRATION === $transaction->type;
        });
    }

    protected function isHaveBurnedTransactions(): bool
    {
        return (bool) $this->getBurnedTransactions()->count();
    }

    protected function getBurnedAmount(): float
    {
        return $this->getBurnedTransactions()->sum(function (Transaction $transaction) {
            return abs($transaction->balance_change);
        });
    }

    protected function getExpiringTransactions(): Collection
    {
        return $this->transactions->filter(function (Transaction $transaction) {
            return Transaction::TYPE_EXPIRATION !== $transaction->type;
        });
    }

    protected function isHaveExpiringTransactions(): bool
    {
        return (bool) $this->getExpiringTransactions();
    }

    protected function getExpiringAmount(): float
    {
        return $this->getExpiringTransactions()->sum(function (Transaction $transaction) {
            return $transaction->output_balance;
        });
    }

    protected function getTemplateName(): string
    {
        return 'emails.burned-points-summary';
    }

    protected function getTemplateVariables(): array
    {
        return [
            'partner' => $this->partner,
            'store' => $this->partner->title,
            'transactions' => $this->transactions,
            'isHaveBurnedTransactions' => $this->isHaveBurnedTransactions(),
            'isHaveExpiringTransactions' => $this->isHaveExpiringTransactions(),
            'burnedAmount' => $this->getBurnedAmount(),
            'expiringAmount' => $this->getExpiringAmount(),
            'balance' => $this->user->balance,
            'link' => app(PartnerService::class)->getEmbeddedUrlAutologin($this->user),
        ];
    }

    protected function getSubject(): string
    {
        if ($this->isHaveBurnedTransactions()) {
            return __('Find out how many unused loyalty points burned in the %store% store last week', [
                'store' => $this->partner->title,
            ]);
        }

        return __('Find out how many unused loyalty points will be burned at %store% within %days% days', [
            'store' => $this->partner->title,
            'days' => self::STATISTIC_INTERVAL_IN_DAYS,
        ]);
    }
}
