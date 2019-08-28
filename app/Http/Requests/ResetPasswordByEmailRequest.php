<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $token
 */
class ResetPasswordByEmailRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string',
        ];
    }
}
