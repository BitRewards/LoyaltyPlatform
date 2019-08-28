<?php

namespace App\Http\Requests\Admin;

use Backpack\CRUD\app\Http\Requests\CrudRequest;

class UpdateActionRequest extends CrudRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->route('action'));
    }
}
