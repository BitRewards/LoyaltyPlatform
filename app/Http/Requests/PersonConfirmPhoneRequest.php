<?php
/**
 * PersonAddEmailRequest.php
 * Creator: lehadnk
 * Date: 17/08/2018.
 */

namespace App\Http\Requests;

class PersonConfirmPhoneRequest extends BaseFormRequest
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
