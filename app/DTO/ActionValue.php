<?php

namespace App\DTO;

class ActionValue extends DTO
{
    public $value;
    public $valueType;

    public function __construct(float $value, string $valueType)
    {
        $this->value = $value;
        $this->valueType = $valueType;
    }
}
