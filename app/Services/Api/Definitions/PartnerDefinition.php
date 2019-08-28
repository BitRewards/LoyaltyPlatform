<?php

namespace App\Services\Api\Definitions;

class PartnerDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Partner';
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
                'title' => ['type' => 'string'],
                'url' => ['type' => 'string'],
            ],
            'required' => ['title', 'url'],
        ];
    }
}
