<?php

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $phone
 * @property string  $password
 */
class SetPasswordRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'string',
            'phone' => 'string',
            'password' => 'required|string',
        ];
    }
}
