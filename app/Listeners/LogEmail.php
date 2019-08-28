<?php

namespace App\Listeners;

use App\Models\SentEmail;
use Illuminate\Mail\Events\MessageSending;

class LogEmail
{
    public function handle(MessageSending $event)
    {
        $message = $event->message;

        foreach ($message->getTo() as $key => $value) {
            $email = filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : $key;
            $sentEmail = new SentEmail();
            $sentEmail->email = $email;
            $sentEmail->subject = $message->getSubject();
            $sentEmail->body = $message->getBody();
            $sentEmail->token = str_random(16);
            $sentEmail->partner_id = $event->data['partner']->id ?? null;
            $sentEmail->save();
        }
    }
}
