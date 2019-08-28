<?php

namespace App\Mail;

use App\Mail\Base\GuestNotification;
use App\Models\Partner;

class GuestEmailConfirmation extends GuestNotification
{
    private $token;

    public function __construct(Partner $partner, string $email, string $token)
    {
        parent::__construct($partner, $email);
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
