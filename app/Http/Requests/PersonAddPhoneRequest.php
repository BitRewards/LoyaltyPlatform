<?php
/**
 * PersonAddEmailRequest.php
 * Creator: lehadnk
 * Date: 17/08/2018.
 */

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * Class PersonAddPhoneRequest.
 *
 * @property string phone
 * @property string confirm_sms
 * @property Partner partner
 */
class PersonAddPhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return (bool) \Auth::user();
    }

    public function rules()
    {
        return [
            'phone' => 'required|string',
            'confirm_sms' => 'required|string',
        ];
    }
}
