<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 * @property string  $name
 */
class ProvideEmailRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return (bool) \Auth::user();
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255|email',
        ];
    }
}
