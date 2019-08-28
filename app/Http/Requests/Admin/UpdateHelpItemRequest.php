<?php

namespace App\Http\Requests\Admin;

use Backpack\CRUD\app\Http\Requests\CrudRequest;

class UpdateHelpItemRequest extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('helpItem'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'position' => 'required|integer',
        ];
    }
}
