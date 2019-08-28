<?php

namespace App\Http\Requests;

/**
 * @property string $email
 * @property string $message
 */
class Support extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::check();
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255',
            'message' => 'required|max:500',
        ];
    }
}
