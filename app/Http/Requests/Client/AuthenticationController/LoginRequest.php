<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:32 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * Class LoginRequest.
 *
 * @property Partner $partner
 * @property $email
 * @property $phone
 * @property $password
 * @property $referrer_id
 * @property $referrer_key
 */
class LoginRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'string',
            'phone' => 'string',
            'password' => 'required|string',
            'referrer_id' => 'string',
            'referrer_key' => 'string',
        ];
    }
}
