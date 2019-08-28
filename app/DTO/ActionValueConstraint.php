<?php

namespace App\DTO;

class ActionValueConstraint extends DTO
{
    public $type;
    public $value;

    public function __construct(string $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
