<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $token
 */
class ResetPasswordRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'emailOrPhone' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
