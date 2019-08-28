<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $giftd_id
 * @property string $password
 */
class SetPasswordForPartnerRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('admin');
    }

    public function rules()
    {
        return [
            'giftd_id' => 'int|required',
            'password' => 'min:7|max:255|required',
        ];
    }
}
