<?php

namespace App\Mail;

use App\Administrator;
use App\Mail\Base\AdministratorNotification;
use App\Models\Partner;
use App\Models\User;

class CustomBonusGivenReport extends AdministratorNotification
{
    /**
     * @var User
     */
    protected $receiver;

    /**
     * @var int
     */
    protected $bonus;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var User|null
     */
    protected $actor;

    /**
     * CustomBonusGivenReport constructor.
     *
     * @param Partner   $partner
     * @param User      $receiver
     * @param int       $bonus
     * @param string    $comment  = null
     * @param User|null $actor
     */
    public function __construct(Partner $partner, User $receiver, int $bonus, string $comment = null, Administrator $actor = null)
    {
        parent::__construct($partner->mainAdministrator);

        $this->receiver = $receiver;
        $this->bonus = $bonus;
        $this->comment = $comment;
        $this->actor = $actor;
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return 'emails.custom-bonus-report';
    }

    /**
     * @return array
     */
    protected function getTemplateVariables(): array
    {
        return [
            'receiver' => $this->receiver,
            'actor' => $this->actor,
            'bonus' => $this->bonus,
            'comment' => $this->comment,
        ];
    }

    /**
     * @return string
     */
    protected function getSubject(): string
    {
        return __('Manual bonuses addition notification');
    }
}
