<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:09 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * Class ValidatePhoneRequest.
 *
 * @property string  $phone
 * @property string  $token
 * @property Partner $partner;
 */
class ValidatePhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|phone',
            'token' => 'required|size:6',
        ];
    }
}
