<?php

namespace App\Services\Api\Definitions;

class TransactionDefinition implements ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name()
    {
        return 'Transaction';
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
                'promoCode' => ['type' => 'string'],
                'promoCodeExpires' => ['type' => 'integer', 'format' => 'int32'],
                'promoCodeExpiresInSeconds' => ['type' => 'integer', 'format' => 'int32'],
                'canPromoCodeBeApplied' => ['type' => 'boolean'],
                'title' => ['type' => 'string'],
                'balanceChange' => ['type' => 'integer', 'format' => 'int32'],
                'balanceChangeStr' => ['type' => 'string'],
                'created' => ['type' => 'string'],
                'status' => ['type' => 'string'],
                'statusStr' => ['type' => 'string'],
            ],
            'required' => ['id', 'promoCode', 'title', 'balanceChange', 'balanceChangeStr', 'status', 'statusStr'],
        ];
    }
}
