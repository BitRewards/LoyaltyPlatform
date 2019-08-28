<?php

namespace App\Http\Requests\Admin;

use App\Models\Action;
use Backpack\CRUD\app\Http\Requests\CrudRequest;

class CreateActionRequest extends CrudRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Action::class);
    }
}
