<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;

class GetAvailableRewards extends BaseFormRequest
{
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'user_key' => 'string|max:255',
        ];
    }
}
