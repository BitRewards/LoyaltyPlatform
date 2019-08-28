<?php

namespace App\Http\Requests;

use App\Models\Credential;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $email
 */
class SignupClientByEmailRequest extends SignupClientRequest
{
    public function rules()
    {
        return array_merge([
            'email' => 'required|max:255|email',
        ], parent::rules());
    }

    protected function getExistingCredential(): ?Credential
    {
        $credential = Credential::model()->where('email', '=', $this->email)->first();

        return $credential;
    }
}
