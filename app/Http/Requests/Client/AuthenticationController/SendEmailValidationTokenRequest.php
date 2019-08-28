<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:16 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;
use App\Models\Partner;

/**
 * Class SendEmailValidationTokenRequest.
 *
 * @property string  $email
 * @property Partner $partner
 */
class SendEmailValidationTokenRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'required|max:255|email',
        ];
    }
}
