<?php

namespace App\DTO;

class ActionValuePolicyRule extends DTO
{
    public $condition;
    public $valueType;
    public $value;

    public function __construct(array $condition, string $valueType, $value)
    {
        $this->condition = $condition;
        $this->valueType = $valueType;
        $this->value = $value;
    }
}
