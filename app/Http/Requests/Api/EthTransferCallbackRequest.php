<?php

namespace App\Http\Requests\Api;

class EthTransferCallbackRequest extends TreasuryRequest
{
    public function rules()
    {
        return [
            'treasury_tx_hash' => 'required|string',
            'amount' => 'required|numeric',
            'treasury_sender_address' => 'required|ethaddress',
            'treasury_receiver_address' => 'required|ethaddress',
        ];
    }
}
