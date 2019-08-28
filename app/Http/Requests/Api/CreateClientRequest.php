<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

/**
 * Class CreateClientRequest.
 *
 * @property $email
 * @property $phone
 * @property $token
 * @property $name
 * @property $promo_code
 * @property $return_existing_user
 * @property $force_email_confirmation
 */
class CreateClientRequest extends BaseFormRequest
{
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'token' => 'max:255',
            'name' => 'max:255',
            'phone' => 'max:64',
            'email' => 'max:255',
            'promo_code' => 'max:255',
            'return_existing_user' => 'boolean',
            'force_email_confirmation' => 'boolean',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function resourceKey(): string
    {
        return implode(':', [
            'user',
            $this->user()->partner->id,
            $this->token,
            $this->email,
            $this->phone,
        ]);
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if ($this->token) {
            $this->token = trim($this->token);

            if ($this->token) {
                $digitsOnly = preg_replace('/[^0-9]+/', '', $this->input('token'));

                if (strlen($digitsOnly) < 8) {
                    $messageBag->add('token', __('The code must consist of at least 8 numerals'));
                }
            }
        }
    }
}
