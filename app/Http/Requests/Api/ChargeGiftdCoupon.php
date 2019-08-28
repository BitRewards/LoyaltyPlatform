<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $token
 * @property float   $amount_total
 * @property string  $comment
 */
class ChargeGiftdCoupon extends BaseFormRequest
{
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'token' => 'required|max:255',
            'amount_total' => 'required|amount',
            'comment' => 'max:100',
        ];
    }

    public function messages()
    {
        return [
            'amount_total.required' => __('Order amount required'),
        ];
    }
}
