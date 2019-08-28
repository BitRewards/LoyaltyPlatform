<?php

namespace App\Http\Requests\Admin;

use App\Models\HelpItem;
use Backpack\CRUD\app\Http\Requests\CrudRequest;

class CreateHelpItemRequest extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', HelpItem::class);
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
