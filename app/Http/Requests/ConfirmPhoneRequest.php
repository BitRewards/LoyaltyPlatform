<?php

namespace App\Http\Requests;

/**
 * @property Partner $partner
 * @property string  $token
 */
class ConfirmPhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::user();
    }

    public function rules()
    {
        return [
            'token' => 'required|size:6',
        ];
    }
}
