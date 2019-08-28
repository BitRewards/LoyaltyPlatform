<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\User;

class ConfirmEmail extends UserNotification
{
    private $token;

    public function __construct(User $user, string $token)
    {
        parent::__construct($user);
        $this->token = $token;
    }

    protected function getTemplateName(): string
    {
        return 'emails.confirm-email';
    }

    protected function getTemplateVariables(): array
    {
        $link = routePartner($this->partner, 'client.confirmEmail', [
            'token' => $this->token,
        ]);

        return compact('link');
    }

    protected function getSubject(): string
    {
        return __('Confirm your email');
    }
}
