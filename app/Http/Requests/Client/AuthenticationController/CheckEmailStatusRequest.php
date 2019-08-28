<?php

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;

/**
 * @property \App\Models\Partner $partner
 * @property string              $email
 */
class CheckEmailStatusRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255|email',
        ];
    }
}
