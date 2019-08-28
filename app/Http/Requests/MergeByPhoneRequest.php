<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $phone
 * @property string  $token
 */
class MergeByPhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'phone' => 'required|phone',
            'token' => 'required|size:6',
        ];
    }
}
