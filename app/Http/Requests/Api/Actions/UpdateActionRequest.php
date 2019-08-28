<?php

namespace App\Http\Requests\Api\Actions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('action'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'type' => 'string|in:'.implode(',', \HAction::getAllTypes()),
            'value' => 'integer',
            'value_type' => 'string|in:fixed,percent',
            'status' => 'string|in:enabled,disabled',
            'tag' => 'string|max:255',
            'description' => 'string',
            'config' => 'string',
            'partner_id' => 'integer|exists:partners,id',
        ];
    }
}
