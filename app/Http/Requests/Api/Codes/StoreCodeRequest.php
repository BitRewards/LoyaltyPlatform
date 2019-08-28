<?php

namespace App\Http\Requests\Api\Codes;

use App\Models\Code;
use Illuminate\Foundation\Http\FormRequest;

class StoreCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Code::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|string|unique:codes,token,NULL,id,partner_id,'.$this->input('partner_id'),
            'bonus_points' => 'required|numeric',
            'partner_id' => 'required|integer|exists:partners,id',
        ];
    }
}
