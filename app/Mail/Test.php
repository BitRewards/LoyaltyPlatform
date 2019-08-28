<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;

class Test extends UserNotification
{
    protected function getTemplateName(): string
    {
        return 'emails.test';
    }

    protected function getTemplateVariables(): array
    {
        return [];
    }

    protected function getSubject(): string
    {
        return 'Wow wow wow!';
    }
}
