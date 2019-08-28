<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\User;
use App\Services\PartnerService;

class BalanceChanged extends UserNotification
{
    public $oldBalance;

    public $isUnsubscribable = true;

    public function __construct(User $user, $oldBalance)
    {
        parent::__construct($user);
        $this->oldBalance = $oldBalance;
    }

    protected function getTemplateName(): string
    {
        return 'emails.balance-changed';
    }

    protected function getTemplateVariables(): array
    {
        $link = $this->partner->isEmailAutoLoginDisabled() ?
            app(PartnerService::class)->getEmbeddedUrl($this->partner, $this->partner->getAppRootUrlAttribute().'#balance') :
            app(PartnerService::class)->getEmbeddedUrlAutologin($this->user);

        return [
            'user' => $this->user,
            'oldBalance' => $this->oldBalance,
            'currentBalanceStr' => $this->getCurrentBalanceFormatted(),
            'forceFiatForAllTransactions' => $this->user->partner->isFiatReferralEnabled(),
            'isWithdrawDisabled' => $this->user->partner->isWithdrawDisabled(),
            'link' => $link,
        ];
    }

    private function getCurrentBalanceFormatted()
    {
        $balance = $this->user->partner->isBitrewardsEnabled()
            ? \HAmount::floor($this->user->balance)
            : $this->user->balance;

        if ($this->user->partner->isFiatReferralEnabled()) {
            $balanceStr = \HAmount::fShort(\HAmount::pointsToFiat($balance, $this->user->partner), $this->user->partner->currency);
        } else {
            $balanceStr = \HAmount::points($balance);
        }

        return $balanceStr;
    }

    protected function getSubject(): string
    {
        return
            $this->oldBalance < $this->user->balance ?
                __('Your balance is %s! :)', $this->getCurrentBalanceFormatted()) :
                __('Your balance is %s', $this->getCurrentBalanceFormatted());
    }
}
