<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Traits\UserByKey;
use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property Partner $partner
 * @property string  $token
 */
class SendSmsConfirmation extends BaseFormRequest
{
    use UserByKey;

    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if (!$this->getUserByKey()->phone) {
            $messageBag->add('user_key', 'User has no phone number');
        }
    }
}
