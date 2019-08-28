<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Traits\RewardById;
use App\Http\Requests\Traits\UserByKey;
use App\Models\Partner;

/**
 * @property Partner $partner
 * @property string  $token
 */
class AcquireReward extends BaseFormRequest
{
    use UserByKey;
    use RewardById;

    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    public function rules()
    {
        return [
            'user_key' => 'required',
        ];
    }
}
