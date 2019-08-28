<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class MassAwardRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //$isSamePartner = (\Auth::user()->partner_id == $this->partner_id);
        $hasAccess = \Auth::check() && (\Auth::user()->can('admin'));

        return $hasAccess;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'partner_id' => 'required|integer',
            'points' => 'required|numeric',
            'onlyConfirmedEmails' => 'boolean',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
        ];
    }
}
