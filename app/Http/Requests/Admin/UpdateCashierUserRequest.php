<?php

namespace App\Http\Requests\Admin;

use Backpack\CRUD\app\Http\Requests\CrudRequest;
use Illuminate\Validation\Rule;

class UpdateCashierUserRequest extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('partner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->route('cashierUser');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('administrators')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('administrators')->ignore($user->id),
            ],
        ];
    }
}
