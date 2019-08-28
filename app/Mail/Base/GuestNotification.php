<?php

namespace App\Mail\Base;

use App\Models\AbstractModel;
use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

abstract class GuestNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Partner
     */
    public $partner;

    abstract protected function getTemplateName(): string;

    abstract protected function getTemplateVariables(): array;

    abstract protected function getSubject(): string;

    public function __construct(Partner $partner, $email)
    {
        $this->partner = $partner;
        $this->to($email);
    }

    protected function setupSender()
    {
        if ($this->partner) {
            $senderName = $this->partner->title;
        } else {
            $senderName = 'GIFTD';
        }

        $result = $this
            ->from('no-reply@bitrewards.email', $senderName);

        return $result;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        foreach (get_object_vars($this) as $property => $value) {
            if ($value instanceof AbstractModel) {
                $value->refresh();
            }
        }

        $template = $this->getTemplateName();

        if (!$this instanceof BitRewardsNotification) {
            \HLanguage::setLanguage($this->partner ? $this->partner->default_language : \HLanguage::LANGUAGE_EN);
            \HContext::setPartner($this->partner);
        }

        $vars = $this->getTemplateVariables();

        if (!isset($vars['partner'])) {
            $vars['partner'] = $this->partner;
        }

        $result =
            $this
                ->setupSender()
                ->subject($this->getSubject())
                ->view($template, $vars);

        if (!$this instanceof BitRewardsNotification) {
            \HContext::restorePartner();
            \HLanguage::restorePreviousLanguage();
        }

        return $result;
    }

    public function renderHtml()
    {
        $this->build();

        return view($this->view, $this->viewData);
    }

    public function send(MailerContract $mailer)
    {
        \HLanguage::setLanguage($this->partner ? $this->partner->default_language : \HLanguage::LANGUAGE_EN);

        try {
            parent::send($mailer);
        } catch (\Exception $ex) {
        }

        \HLanguage::restorePreviousLanguage();
    }
}
