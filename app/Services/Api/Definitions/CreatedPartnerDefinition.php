<?php

namespace App\Services\Api\Definitions;

class CreatedPartnerDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Created Partner';
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
                'key' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'password' => ['type' => 'string'],
                'api_token' => ['type' => 'string'],
            ],
        ];
    }
}
