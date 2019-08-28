<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;
use App\Models\User;

class ConfirmMergeByEmail extends UserNotification
{
    private $token;

    public function __construct(User $user, string $token)
    {
        parent::__construct($user);
        $this->token = $token;
    }

    protected function getTemplateName(): string
    {
        return 'emails.confirm-merge-by-email';
    }

    protected function getTemplateVariables(): array
    {
        return ['token' => $this->token, 'partner' => $this->partner];
    }

    protected function getSubject(): string
    {
        return __('Your confirmation code: %s', $this->token);
    }
}
