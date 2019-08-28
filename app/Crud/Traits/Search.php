<?php

namespace App\Crud\Traits;

use Illuminate\Database\Query\Expression;

trait Search
{
    /**
     * Apply the search logic for each CRUD column.
     */
    public function applySearchLogicForColumn($query, $column, $searchTerm)
    {
        $columnType = $column['type'];

        // if there's a particular search logic defined, apply that one
        if (isset($column['searchLogic'])) {
            $searchLogic = $column['searchLogic'];

            // if a closure was passed, execute it
            if (is_callable($searchLogic)) {
                return $searchLogic($query, $column, $searchTerm);
            }

            // if a string was passed, search like it was that column type
            if (is_string($searchLogic)) {
                $columnType = $searchLogic;
            }

            // if false was passed, don't search this column
            if (false == $searchLogic) {
                return;
            }
        }

        // sensible fallback search logic, if none was explicitly given
        if ($column['tableColumn']) {
            switch ($columnType) {
                case 'email':
                case 'date':
                case 'datetime':
                case 'text':
                    $query->orWhere(new Expression($column['name'].'::text'), 'ilike', '%'.$searchTerm.'%');

                    break;

                case 'select':
                case 'select_multiple':
                    $query->orWhereHas($column['entity'], function ($q) use ($column, $searchTerm) {
                        $q->where(new Expression($column['attribute'].'::text'), 'ilike', '%'.$searchTerm.'%');
                    });

                    break;

                default:
                    return;

                    break;
            }
        }
    }
}
