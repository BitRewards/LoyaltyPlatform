<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class EthereumToLower extends TransformsRequest
{
    /**
     * The attributes that should be lowercased.
     *
     * @var array
     */
    protected $processableAttributes = [
        'tx_id',
        'treasury_tx_hash',
        'treasury_sender_address',
        'treasury_receiver_address',
    ];

    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (!in_array($key, $this->processableAttributes, true)) {
            return $value;
        }

        return is_string($value) ? trim(mb_strtolower($value)) : $value;
    }
}
