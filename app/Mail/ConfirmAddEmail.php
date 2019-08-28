<?php

namespace App\Mail;

use App\Mail\Base\PersonNotification;
use App\Models\Partner;
use App\Models\Person;

class ConfirmAddEmail extends PersonNotification
{
    private $token;

    public function __construct(Partner $partner, Person $person, string $email, string $token)
    {
        parent::__construct($partner, $person, $email);
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
