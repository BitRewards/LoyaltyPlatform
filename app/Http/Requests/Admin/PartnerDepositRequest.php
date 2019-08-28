<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PartnerDepositRequest extends FormRequest
{
    public const FEE_TYPE_PERCENT = 'percent';
    public const FEE_TYPE_FIXED = 'fixed';

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'fee' => 'required|numeric|min:0|max:100',
            'fee_type' => 'required|in:percent,fixed',
            'partner_id' => Rule::exists('partners', 'id'),
        ];
    }
}
