<?php

namespace App\Http\Requests;

/**
 * @property \App\Models\Partner $partner
 * @property string              $email
 */
class SendEmailVerificationCodeRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255|email',
        ];
    }
}
