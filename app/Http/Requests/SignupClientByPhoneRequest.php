<?php

namespace App\Http\Requests;

use App\Models\Credential;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $phone
 */
class SignupClientByPhoneRequest extends SignupClientRequest
{
    public function rules()
    {
        return array_merge([
            'phone' => 'required|phone',
        ], parent::rules());
    }

    protected function getExistingCredential(): ?Credential
    {
        $credential = Credential::model()->where('phone', '=', \HUser::normalizePhone($this->phone, $this->partner->default_country))->first();

        return $credential;
    }
}
