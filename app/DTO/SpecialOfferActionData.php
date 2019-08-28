<?php

namespace App\DTO;

class SpecialOfferActionData extends DTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $brand;

    /**
     * @var string
     */
    public $image;

    /**
     * @var ActionData
     */
    public $action;

    public function __construct()
    {
        $this->action = new ActionData();
    }
}
