<?php

namespace App\Http\Requests\Admin;

use Backpack\CRUD\app\Http\Requests\CrudRequest;

class CashierUserRequest extends CrudRequest
{
    public function authorize()
    {
        return $this->user()->can('partner');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:255|unique:administrators',
        ];
    }
}
