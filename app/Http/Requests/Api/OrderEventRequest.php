<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('partner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required',
            /*'email' => 'email',
            'phone' => 'string|max:255',
            'user_crm_key' => 'string|max:255',
            'ref_user_crm_key' => 'string|max:255',
            'status_autofinishes_at' => 'date_format:Y-m-d H:i:s',
            'name' => 'string|max:255',
            'amount_total' => 'integer',
            'is_paid' => 'boolean',
            'is_delivered' => 'boolean',
            'comment' => 'string',
            'manager_comment' => 'string',
            'promo_codes' => 'array',
            'process_immediately' => 'boolean',*/
        ];
    }
}
