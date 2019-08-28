<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Traits\UserByKey;

class DetachCodeRequest extends BaseFormRequest
{
    use UserByKey;

    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('partner-or-cashier');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
