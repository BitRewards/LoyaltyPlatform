<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\User;

class ResetPassword extends UserNotification
{
    private $token;
    private $email;

    public function __construct(User $user, string $token)
    {
        parent::__construct($user);
        $this->token = $token;
        $this->email = $user->email;
    }

    protected function getTemplateName(): string
    {
        return 'emails.reset-password';
    }

    protected function getTemplateVariables(): array
    {
        $link = routeEmbedded($this->partner, 'client.resetByEmailRequest', [
            'emailOrPhone' => $this->email,
            'token' => $this->token,
        ]);

        return compact('link');
    }

    protected function getSubject(): string
    {
        return __('Password restore request');
    }
}
