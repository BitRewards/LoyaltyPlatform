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
 * Class SendPasswordResetTokenRequest.
 *
 * @property string  $email
 * @property string  $phone
 * @property Partner $partner
 */
class SendPasswordResetTokenRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'max:255|email',
            'phone' => 'phone',
        ];
    }
}
