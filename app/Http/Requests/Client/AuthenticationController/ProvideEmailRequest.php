<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 5:34 AM.
 */

namespace App\Http\Requests\Client\AuthenticationController;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ProvideEmailRequest.
 *
 * @property string $token
 * @property string $email
 */
class ProvideEmailRequest extends BaseFormRequest
{
    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string',
        ];
    }
}
