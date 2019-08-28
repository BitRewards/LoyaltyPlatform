<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Traits\PartnerUniqueEmail;

class PartnerRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    use PartnerUniqueEmail;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:2|max:255',
            'email' => 'required|max:255|email',
            'giftd_id' => 'int|unique',
            'customizations' => 'json',
            'settings' => 'json',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
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
