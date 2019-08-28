<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\User;
use App\Services\PartnerService;

class PositiveBalance extends UserNotification
{
    public $isUnsubscribable = true;

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    protected function getTemplateName(): string
    {
        return 'emails.positive-balance';
    }

    protected function getTemplateVariables(): array
    {
        return [
            'user' => $this->user,
            'link' => app(PartnerService::class)->getEmbeddedUrlAutologin($this->user),
        ];
    }

    protected function getSubject(): string
    {
        $balance = $this->user->partner->isBitrewardsEnabled()
            ? \HAmount::floor($this->user->balance)
            : $this->user->balance;

        return  __('Your balance is %s', \HAmount::points($balance));
    }
}
