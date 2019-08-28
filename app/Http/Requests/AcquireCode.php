<?php

namespace App\Http\Requests;

use App\Models\Code;
use App\Models\Partner;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property Partner $partner
 * @property string  $token
 */
class AcquireCode extends BaseFormRequest
{
    private $code;

    public function authorize()
    {
        return !\Auth::guest();
    }

    public function rules()
    {
        return [
            'token' => 'required|max:255',
        ];
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
        if (!$messageBag->isEmpty()) {
            return;
        }

        $token = Code::normalizeToken($this->token);

        $this->code = Code::model()->whereAttributes(['partner_id' => $this->partner->id, 'token' => $token])->first();

        if (!$this->code) {
            $messageBag->add('token', __('Code not found'));

            return;
        }

        if ($this->code->user_id == \Auth::user()->id) {
            $messageBag->add('token', __('This code has already been applied  to your account'));
        }
    }

    public function getCode()
    {
        return $this->code;
    }
}
