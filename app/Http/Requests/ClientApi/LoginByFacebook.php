<?php

namespace App\Http\Requests\ClientApi;

use App\Http\Requests\BaseFormRequest;

/**
 * @property \App\Models\Partner $partner
 * @property string              $access_token
 */
class LoginByFacebook extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'access_token' => 'required|max:255',
        ];
    }
}
