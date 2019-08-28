<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class CodesBulkImportRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isSamePartner = (\Auth::user()->partner_id == $this->partner_id);
        $hasAccess = \Auth::check() && (\Auth::user()->can('admin') || $isSamePartner);

        return $hasAccess;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'partner_id' => 'required|integer',
            'bonus_points' => 'required|numeric',
            'tokens' => 'required|string',
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
