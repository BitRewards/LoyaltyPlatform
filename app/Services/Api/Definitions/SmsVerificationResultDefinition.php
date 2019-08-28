<?php

namespace App\Services\Api\Definitions;

class SmsVerificationResultDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'SmsVerificationResult';
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
                'result' => ['type' => 'boolean'],
            ],
            'required' => ['result'],
        ];
    }
}
