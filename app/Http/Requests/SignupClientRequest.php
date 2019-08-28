<?php

namespace App\Http\Requests;

use App\Models\Credential;
use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;
use App\Services\UserService;

/**
 * @property Partner $partner
 */
abstract class SignupClientRequest extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::guest();
    }

    public function rules()
    {
        return [
            'password' => 'required|max:64',
            'referrer_id' => 'nullable|integer',
            'referrer_key' => 'nullable|string',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        $existingCredential = $this->getExistingCredential();

        if ($existingCredential) {
            $isNoPassword = empty($existingCredential->password);
            $password = $this->password.Credential::STATIC_PASSWORD_SALT;

            if ($isNoPassword && $existingCredential->person->hasSocialNetworkId()) {
                $socialServiceName = $existingCredential->person->getAvailableSocialNetworkNames();
                $messageBag->add('password', __('Get authorized in %s or restore your password!', $socialServiceName));
            } elseif (!\Hash::check($password, $existingCredential->password)) {
                $messageBag->add('password', _('Wrong password!'));
            }
        } elseif (mb_strlen($this->password) < UserService::MIN_PASSWORD_LENGTH) {
            $messageBag->add('password', __('Password must be at least %d characters!', UserService::MIN_PASSWORD_LENGTH));
        }
    }

    abstract protected function getExistingCredential(): ?Credential;
}
