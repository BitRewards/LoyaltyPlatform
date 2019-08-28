<?php

namespace App\Http\Requests;

use App\Models\Partner;

/**
 * @property Partner $partner
 */
class PartnerPageRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
