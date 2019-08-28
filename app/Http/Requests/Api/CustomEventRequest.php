<?php

namespace App\Http\Requests\Api;

use App\Models\StoreEvent;
use Illuminate\Foundation\Http\FormRequest;

class CustomEventRequest extends FormRequest
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
        return [
            'entity_type' => 'required|string',
            'entity_external_id' => 'integer',
            'action' => 'string|in:'.implode(',', StoreEvent::actions()),
        ];
    }
}
