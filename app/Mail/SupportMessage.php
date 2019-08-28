<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Mail\Mailable;

class SupportMessage extends Mailable
{
    /**
     * @var Partner
     */
    public $partner;
    /**
     * @var User
     */
    public $sender;

    public $text;

    public $desiredEmail;

    public function __construct(User $sender, $text, $desiredEmail)
    {
        $this->partner = $sender->partner;
        $this->sender = $sender;
        $this->text = $text;
        $this->desiredEmail = $desiredEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = 'emails.support-message';

        return $this
            ->from('no-reply@bitrewards.email', 'BitRewards')
            ->to(\App::isLocal() ? '**REMOVED**' : '**REMOVED**')
            ->subject(__('Contact rewards program support (%s)', $this->partner->title))
            ->view($template);
    }
}
