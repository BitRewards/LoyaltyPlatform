<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class WalletExchangeRequest extends WalletTransactionsRequest
{
    public function rules()
    {
        return [
            'wallet' => [
                'required',
                Rule::in(['bitrewards', 'external']),
            ],
            'address' => 'required|ethaddress',
        ];
    }
}
