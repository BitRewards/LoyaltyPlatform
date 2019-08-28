<?php

namespace App\Crud;

use App\Traits\RelationFilter;
use Backpack\CRUD\app\Http\Controllers\CrudController as BaseController;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property CrudPanel $crud
 */
class CrudController extends BaseController
{
    use RelationFilter;

    public function __construct()
    {
        if (!$this->crud) {
            $this->crud = app()->make(CrudPanel::class);

            // call the setup function inside this closure to also have the request there
            // this way, developers can use things stored in session (auth variables, etc)
            $this->middleware(function ($request, $next) {
                $this->request = $request;
                $this->crud->request = $request;
                $this->setup();

                return $next($request);
            });
        }

        parent::__construct();
    }

    protected function getCrudQuery(): Builder
    {
        return $this->crud->query;
    }
}
