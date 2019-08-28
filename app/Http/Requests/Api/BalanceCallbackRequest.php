<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\JsonRequest;

class BalanceCallbackRequest extends JsonRequest
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

    public function rules()
    {
        return [
            'id' => 'required|int',
            'tx_id' => 'string|nullable',
        ];
    }
}
