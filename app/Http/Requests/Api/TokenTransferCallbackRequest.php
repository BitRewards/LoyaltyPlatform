<?php

namespace App\Http\Requests\Api;

class TokenTransferCallbackRequest extends TreasuryRequest
{
    public function rules()
    {
        return [
            'treasury_tx_hash' => 'required|string',
            'amount' => 'required|numeric',
            'treasury_data' => 'string|nullable',
            'treasury_sender_address' => 'required|ethaddress',
        ];
    }
}
