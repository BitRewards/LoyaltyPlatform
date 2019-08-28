<?php

namespace App\Http\Requests\Api\Actions;

use App\Models\Action;
use Illuminate\Foundation\Http\FormRequest;

class StoreActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Action::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', \HAction::getAllTypes()),
            'value' => 'required|integer',
            'value_type' => 'required|string|in:fixed,percent',
            'status' => 'required|string|in:enabled,disabled',
            'tag' => 'string|max:255',
            'description' => 'string',
            'config' => 'string',
            'partner_id' => 'required|integer|exists:partners,id',
        ];
    }
}
