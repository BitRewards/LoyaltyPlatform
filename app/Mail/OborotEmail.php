<?php

namespace App\Mail;

use App\Mail\Base\UserNotification;

class OborotEmail extends UserNotification
{
    protected function getTemplateName(): string
    {
        return 'emails.oborot-email';
    }

    protected function setupSender()
    {
        $senderName = 'BitRewards';

        $result = $this
            ->from('oborot@bitrewards.com', $senderName);

        return $result;
    }

    protected function getTemplateVariables(): array
    {
        return [
            'user' => $this->user,
        ];
    }

    protected function getSubject(): string
    {
        return 'Промо-код OBOROT на скидку 5000 рублей на продукты BitRewards';
    }
}
