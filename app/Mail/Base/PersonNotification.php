<?php

namespace App\Mail\Base;

use App\Models\AbstractModel;
use App\Models\Partner;
use App\Models\Person;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

abstract class PersonNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Partner
     */
    public $partner;

    /**
     * @var Person
     */
    public $person;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $email;

    public $isUnsubscribable = false;

    abstract protected function getTemplateName(): string;

    abstract protected function getTemplateVariables(): array;

    abstract protected function getSubject(): string;

    public function __construct(Partner $partner, Person $person, string $email)
    {
        $this->partner = $partner;
        $this->person = $person;
        $this->user = $person->getUser($partner->id);
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

        \HLanguage::setLanguage($this->partner ? $this->partner->default_language : \HLanguage::LANGUAGE_EN);
        \HContext::setPartner($this->partner);

        $vars = $this->getTemplateVariables();
        $vars['isUnsubscribable'] = $this->isUnsubscribable;

        if (!isset($vars['partner'])) {
            $vars['partner'] = $this->partner;
        }

        if (!isset($vars['user'])) {
            $vars['user'] = $this->user;
        }

        $result = $this
            ->setupSender()
            ->subject($this->getSubject())
            ->view($template, $vars);

        \HContext::restorePartner();
        \HLanguage::restorePreviousLanguage();

        return $result;
    }

    public function renderHtml()
    {
        $this->build();

        return view($this->view, $this->viewData);
    }

    public function getAutologinToken()
    {
        return app(UserService::class)->getAutologinToken($this->user);
    }

    public function send(MailerContract $mailer)
    {
        if ($this->isUnsubscribable && $this->user->is_unsubscribed) {
            return;
        }

        \HLanguage::setLanguage($this->partner ? $this->partner->default_language : \HLanguage::LANGUAGE_EN);

        try {
            parent::send($mailer);
        } catch (\Exception $ex) {
        }

        \HLanguage::restorePreviousLanguage();
    }
}
