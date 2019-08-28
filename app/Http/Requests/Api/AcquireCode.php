<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Traits\UserByKey;
use App\Models\Code;
use App\Models\Partner;
use App\Services\CodeService;
use Illuminate\Contracts\Support\MessageBag;

/**
 * @property Partner $partner
 * @property string  $token
 */
class AcquireCode extends BaseFormRequest
{
    use UserByKey;

    private $code;

    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
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

        $token = Code::normalizeToken($this->input('token'));
        $partner = $this->user()->partner;

        $this->code = Code::model()->whereAttributes(['partner_id' => $partner->id, 'token' => $token])->first();

        if (!$this->code) {
            $this->code = app(CodeService::class)->createCode($partner, $token);
        }

        if ($this->code->user_id == $this->getUserByKey()->id) {
            $messageBag->add('token', __("This code is already attached to this user's account"));
        }
    }

    public function getCode()
    {
        return $this->code;
    }
}
