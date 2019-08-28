<?php

namespace App\Services\Api\Definitions;

class UserDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'User';
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
                'email' => ['type' => 'string'],
                'key' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'picture' => ['type' => 'string'],
                'phone' => ['type' => 'string'],
                'balance' => ['type' => 'integer', 'format' => 'int32'],
                'codes' => ['type' => 'array', 'items' => ['type' => 'string']],
                'order_comment_tracking_code' => ['type' => 'string'],
                'title' => ['type' => 'string'],
            ],
            'required' => ['email', 'key', 'name', 'phone', 'balance', 'codes', 'order_comment_tracking_code', 'title'],
        ];
    }
}
