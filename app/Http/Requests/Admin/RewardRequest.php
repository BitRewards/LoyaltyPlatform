<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Reward;

class RewardRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->can('update', new Reward($this->all()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
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
