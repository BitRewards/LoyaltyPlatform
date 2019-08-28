<?php

namespace App\Http\Requests;

use App\Models\Partner;
use App\Models\User;

/**
 * @property Partner $partner
 * @property string  $phone
 */
class ProvidePhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return (bool) \Auth::user();
    }

    public function rules()
    {
        return [
            'phone' => 'required|phone',
        ];
    }
}
