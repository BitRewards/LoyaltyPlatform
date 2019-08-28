<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Mail\Mailable;

class ReferralInvite extends Mailable
{
    /**
     * @var Partner
     */
    public $partner;
    /**
     * @var User
     */
    public $sender;

    public $senderName;
    public $inviteText;
    public $email;

    public function __construct(User $sender, $email, $senderName, $inviteText)
    {
        $this->partner = $sender->partner;
        $this->sender = $sender;
        $this->inviteText = $inviteText;
        $this->senderName = $senderName;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \HLanguage::setLanguage($this->sender->partner->default_language);
        $template = 'emails.referral-invite';

        $subject = __('Your friend %s has sent you a discount!', $this->senderName);

        $result = $this
            ->from('no-reply@bitrewards.email', $this->partner->title)
            ->to($this->email)
            ->subject($subject)
            ->view($template);

        \HLanguage::restorePreviousLanguage();

        return $result;
    }
}
