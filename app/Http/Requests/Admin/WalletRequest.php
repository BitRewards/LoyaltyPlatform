<?php

namespace App\Http\Requests\Admin;

class WalletRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->partner && \Auth::user()->partner->isBitrewardsEnabled();
    }
}
