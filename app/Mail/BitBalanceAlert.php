<?php

namespace App\Mail;

use App\Administrator;
use App\Mail\Base\AdministratorNotification;
use App\Services\PartnerService;

class BitBalanceAlert extends AdministratorNotification
{
    public $isUnsubscribable = true;

    public function __construct(Administrator $user)
    {
        parent::__construct($user);
    }

    protected function getTemplateName(): string
    {
        return 'emails.bit-balance.alert';
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
        return __('Balance is not sufficient in ETH-wallet');
    }
}
