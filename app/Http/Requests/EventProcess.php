<?php

namespace App\Http\Requests;

use App\Models\Partner;
use App\Models\Action;

/**
 * @property Partner $partner
 * @property Action  $action
 * @property string  $phone
 */
class EventProcess extends BaseFormRequest
{
    public function authorize()
    {
        return \Auth::user() && \Auth::user()->can('view', $this->action);
    }

    public function rules()
    {
        return [
        ];
    }
}
