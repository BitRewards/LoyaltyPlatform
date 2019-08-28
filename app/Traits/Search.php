<?php

namespace App\Traits;

use App\Crud\CrudPanel;
use LiveControl\EloquentDataTable\ExpressionWithName;

/**
 * @property CrudPanel $crud
 */
trait Search
{
    public function search()
    {
        $this->crud->hasAccessOrFail('list');

        // crate an array with the names of the searchable columns
        $columns = collect($this->crud->columns)
                    ->reject(function ($column, $key) {
                        // the select_multiple columns are not searchable
                        return isset($column['type']) && 'select_multiple' == $column['type'];
                    })
                    ->pluck('name')
                    // add the primary key, otherwise the buttons won't work
                    ->merge($this->crud->model->getKeyName())
                    ->toArray();

        $expressions = [];

        foreach ($columns as $column) {
            if (!empty($column)) {
                $expressions[] = new ExpressionWithName("$column::text", $column);
            }
        }

        // Eager loading
        $relations = [];

        foreach ($this->crud->columns as $column) {
            if (isset($column['type']) && 'select' == $column['type']) {
                $relations[] = $column['entity'];
            }
        }

        if (isset($this->with)) {
            $relations = array_merge($relations, $this->with);
        }

        if (!empty($relations)) {
            $this->crud->query = $this->crud->query->with($relations);
        }

        // structure the response in a DataTable-friendly way
        $dataTable = new \App\Common\LiveControl\BetterDataTable(
            $this->crud,
            $expressions,
            null,
            iso($this->searchableColumns, []),
            iso($this->orderBy, ['id' => 'desc'])
        );

        // make the datatable use the column types instead of just echoing the text
        $dataTable->setFormatRowFunction(function ($entry) {
            // get the actual HTML for each row's cell
            $row_items = $this->crud->getRowViews($entry, $this->crud);

            // add the buttons as the last column
            if ($this->crud->buttons->where('stack', 'line')->count()) {
                $row_items[] = \View::make('crud::inc.button_stack', ['stack' => 'line'])
                                ->with('crud', $this->crud)
                                ->with('entry', $entry)
                                ->render();
            }

            // add the details_row buttons as the first column
            if ($this->crud->details_row) {
                array_unshift($row_items, \View::make('crud::columns.details_row_button')
                                ->with('crud', $this->crud)
                                ->with('entry', $entry)
                                ->render());
            }

            return $row_items;
        });

        return $dataTable->make();
    }
}
