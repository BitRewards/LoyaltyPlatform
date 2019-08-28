<?php

namespace App\Services;

use App\Mail\SupportMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SupportService
{
    /**
     * @var \Mail|Mail
     */
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function sendQuestion(User $sender, string $question, string $replyEmail)
    {
        $message = new SupportMessage(
            $sender,
            $question,
            $replyEmail
        );

        $this->mail::queue($message);
    }
}
