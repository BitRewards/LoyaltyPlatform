<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;

class ListTransactions extends BaseFormRequest
{
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'limit' => 'int',
            'offset' => 'int',
        ];
    }
}
