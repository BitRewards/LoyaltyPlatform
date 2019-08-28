<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\LogRequest as StoreRequest;
use App\Http\Requests\Admin\LogRequest as UpdateRequest;
use App\Traits\Search;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;

class LogCrudController extends CrudController
{
    // FIXME pull request to backpack
    use Search, RelationFilter, CreatedAtFilters;

    public function __construct()
    {
        parent::__construct();

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("App\Models\Log");
        $this->crud->setRoute('admin/log');
        $this->crud->setEntityNameStrings('log', 'logs');
        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');

        $this->crud->setColumns(['id', 'level_name', 'created_at']);
        $this->crud->setColumnDetails('created_at', ['type' => 'date']);

        $this->crud->addField([
            'name' => 'level_name',
            'label' => 'Level name',
            'type' => 'select_from_array',
            'options' => [
                'error' => 'Error',
                'warning' => 'Warning',
            ],
        ]);

        $this->crud->addField([
            'name' => 'message',
            'label' => 'Message',
            'type' => 'textarea',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']);
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
        // $this->crud->enableAjaxTable();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function showDetailsRow($id)
    {
        $log = \App\Models\Log::find($id);

        return "<div style='overflow: hidden; white-space: pre-wrap'><small>{$log->message}</small></div>";
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }
}
