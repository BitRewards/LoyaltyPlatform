<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class WalletWithdrawRequest extends WalletTransactionsRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'currency' => [
                'required',
                Rule::in(\HCurrency::getValues()),
            ],
            'address' => 'required|ethaddress',
        ];
    }
}
