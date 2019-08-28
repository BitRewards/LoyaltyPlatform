<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:34 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * Class ProvidePhoneRequest.
 *
 * @property string  $phone
 * @property string  $token
 * @property Partner $partner
 */
class ProvidePhoneRequest extends BaseFormRequest
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
