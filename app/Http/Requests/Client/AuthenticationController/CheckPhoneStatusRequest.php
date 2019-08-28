<?php

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $phone
 */
class CheckPhoneStatusRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|phone',
        ];
    }
}
