<?php

namespace App\Services\Api\Definitions;

class ActionDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Action';
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
                'type' => ['type' => 'string'],
                'value' => ['type' => 'string'],
                'raw_value' => ['type' => 'integer', 'format' => 'int32'],
                'value_type' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'config' => ['type' => 'string'],
                'partner_id' => ['type' => 'integer', 'format' => 'int32'],
                'status' => ['type' => 'string'],
            ],
            'required' => ['id', 'type', 'value', 'value_type', 'title', 'partner_id', 'status'],
            'example' => [],
        ];
    }
}
