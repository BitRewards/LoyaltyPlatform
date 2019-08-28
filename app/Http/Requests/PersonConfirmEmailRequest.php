<?php
/**
 * PersonAddEmailRequest.php
 * Creator: lehadnk
 * Date: 17/08/2018.
 */

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * Class PersonConfirmEmailRequest.
 *
 * @property Partner $partner
 */
class PersonConfirmEmailRequest extends BaseFormRequest
{
    public function authorize()
    {
        return (bool) \Auth::user();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }
}
