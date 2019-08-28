<?php
/**
 * PersonAddEmailRequest.php
 * Creator: lehadnk
 * Date: 17/08/2018.
 */

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * Class PersonAddEmailRequest.
 *
 * @property Partner partner
 * @property int confirm_code
 * @property int email
 */
class PersonAddEmailRequest extends BaseFormRequest
{
    public function authorize()
    {
        return (bool) \Auth::user();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'confirm_code' => 'required|string',
        ];
    }
}
