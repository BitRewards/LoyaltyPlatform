<?php

namespace App\Services\Api\Definitions;

class CodeDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Code';
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
                'token' => ['type' => 'string'],
                'bonus_points' => ['type' => 'integer'],
                'partner_id' => ['type' => 'integer'],
            ],
            'required' => ['id', 'token', 'bonus_points', 'partner_id'],
        ];
    }
}
