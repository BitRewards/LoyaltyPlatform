<?php

namespace App\Http\Requests\Api\Codes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('code'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'string',
            'bonus_points' => 'numeric',
            'partner_id' => 'integer|exists:partners,id',
        ];
    }
}
