<?php

namespace App\Http\Requests\Api;

class TransactionCallbackRequest extends TreasuryRequest
{
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'failed';

    public static $STATUSES = [
        self::STATUS_CONFIRMED,
        self::STATUS_REJECTED,
    ];

    public function rules()
    {
        return [
            'id' => 'required|int',
            'tx_id' => 'string|nullable',
            'status' => 'required|string|in:'.implode(',', self::$STATUSES),
        ];
    }

    public function isConfirmed()
    {
        return self::STATUS_CONFIRMED === $this->input('status');
    }

    public function isRejected()
    {
        return self::STATUS_REJECTED === $this->input('status');
    }
}
