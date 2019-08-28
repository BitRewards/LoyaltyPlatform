<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;

//use App\Http\Requests\Traits\PartnerUniqueEmail;

class ChangeLanguageRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'default_language' => 'max:2|required',
        ];
    }
}
