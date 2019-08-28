<?php

namespace App\Http\Requests\Admin;

use App\Models\Partner;
use Illuminate\Validation\Rule;

class WalletSettingsRequest extends WalletTransactionsRequest
{
    public function rules()
    {
        return [
            'fee' => 'required|int|min:0|max:1000000',
            'fee_type' => [
                'required',
                Rule::in([Partner::BIT_WITHDRAWAL_FEE_TYPE_PERCENT, Partner::BIT_WITHDRAWAL_FEE_TYPE_FIXED]),
            ],
            'min_withdrawal' => 'required|int|min:1|max:1000000',
        ];
    }
}
