<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $token
 */
class ResetPasswordByPhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'phone' => 'required|string',
            'token' => 'required|size:6',
        ];
    }
}
