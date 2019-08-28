<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:19 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * Class SendPhoneValidationTokenRequest.
 *
 * @property string  $phone
 * @property Partner $partner
 */
class SendPhoneValidationTokenRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'phone' => 'required|phone',
        ];
    }
}
