<?php

namespace App\DTO;

use App\Administrator;
use App\Models\Action;
use App\Models\Code;
use App\Models\User;

class CustomBonusData extends DTO
{
    /**
     * Bonus receiver.
     *
     * @var User
     */
    public $receiver;

    /**
     * Bonus value.
     *
     * @var int
     */
    public $bonus;

    /**
     * Cashier Cashier or Partner user.
     *
     * @var User|null
     */
    public $actor;

    /**
     * Action for which bonus points is issued.
     *
     * @var Action|null
     */
    public $action;

    /**
     * Detached Code related to current bonus.
     *
     * @var Code|null
     */
    public $code;

    /**
     * @var string
     */
    public $comment;

    /**
     * This tag is optional field which is stored in the 'data' field in the database
     * It can be used for searching over the 'transactions' table for locating bonus transactions related to some tag
     * As of 27 sep 2018 it's used only on bulk bonuses give feature.
     *
     * @var string
     */
    public $tag;

    /**
     * CustomBonusData constructor.
     *
     * @param User|null   $receiver
     * @param int|null    $bonus
     * @param User|null   $actor
     * @param Action|null $action
     * @param Code|null   $code
     * @param string|null $comment
     * @param string|null $tag
     */
    public function __construct(User $receiver = null, $bonus = null, Administrator $actor = null, Action $action = null, Code $code = null, string $comment = null, string $tag = null)
    {
        $this->receiver = $receiver;
        $this->bonus = $bonus;
        $this->actor = $actor;
        $this->action = $action;
        $this->code = $code;
        $this->comment = $comment;
        $this->tag = $tag;
    }
}
