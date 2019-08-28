<?php

namespace App\Services\Api\Definitions;

class RewardDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Reward';
    }

    /**
     * Get the array representation of defintion.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'format' => 'int32'],
                'title' => ['type' => 'string'],
                'price' => ['type' => 'number', 'format' => 'float'],
                'discountAmount' => ['type' => 'integer', 'format' => 'int32'],
                'discountPercent' => ['type' => 'integer', 'format' => 'int32'],
                'discountStr' => ['type' => 'string'],
            ],
            'required' => ['id', 'title', 'price', 'discountAmount', 'discountPercent', 'discountStr'],
        ];
    }
}
