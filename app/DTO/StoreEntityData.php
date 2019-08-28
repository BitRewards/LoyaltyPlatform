<?php

namespace App\DTO;

class StoreEntityData extends DTO
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $userCrmKey;

    /**
     * @var string
     */
    public $refUserCrmKey;

    /**
     * The moment in which the event can be considered confirmed; optional.
     */
    public $statusAutoFinishesAt;

    /*
     * Customer name; is used for auto signup of customer
     */
    public $name;
}
