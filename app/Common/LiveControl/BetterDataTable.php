<?php

namespace App\Common\LiveControl;

use App\Crud\CrudPanel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression as raw;
use LiveControl\EloquentDataTable\VersionTransformers\Version110Transformer;
use LiveControl\EloquentDataTable\VersionTransformers\VersionTransformerContract;
use LiveControl\EloquentDataTable\ExpressionWithName;

class BetterDataTable
{
    private $crud;
    private $builder;
    private $columns;
    private $searchableColumns;
    private $formatRowFunction;

    private $orderBy;

    /**
     * @var VersionTransformerContract
     */
    protected static $versionTransformer;

    private $rawColumns;
    private $columnNames;

    private $total = 0;
    private $filtered = 0;

    private $rows = [];

    /**
     * @param CrudPanel     $crud
     * @param array         $columns
     * @param null|callable $formatRowFunction
     *
     * @throws Exception
     */
    public function __construct(CrudPanel $crud, $columns, $formatRowFunction = null, $searchableColumns = [], $orderBy = [])
    {
        $this->crud = $crud;
        $this->setBuilder($crud->query);
        $this->setColumns($columns);
        $this->setSearchableColumns(array_flip($searchableColumns));
        $this->setOrderBy($orderBy);

        if (null !== $formatRowFunction) {
            $this->setFormatRowFunction($formatRowFunction);
        }
    }

    /**
     * @param Builder|Model $builder
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setBuilder($builder)
    {
        if (!($builder instanceof Builder || $builder instanceof Model)) {
            throw new Exception('$builder variable is not an instance of Builder or Model.');
        }

        $this->builder = $builder;

        return $this;
    }

    /**
     * @param mixed $columns
     *
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function setSearchableColumns($columns)
    {
        $this->searchableColumns = $columns;

        return $this;
    }

    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @param callable $function
     *
     * @return $this
     */
    public function setFormatRowFunction($function)
    {
        $this->formatRowFunction = $function;

        return $this;
    }

    /**
     * @param VersionTransformerContract $versionTransformer
     *
     * @return $this
     */
    public function setVersionTransformer(VersionTransformerContract $versionTransformer)
    {
        static::$versionTransformer = $versionTransformer;

        return $this;
    }

    /**
     * Make the datatable response.
     *
     * @return array
     *
     * @throws Exception
     */
    public function make()
    {
        $this->total = $this->crud->count();

        $this->rawColumns = $this->getRawColumns($this->columns);
        $this->columnNames = $this->getColumnNames();

        if (null === static::$versionTransformer) {
            static::$versionTransformer = new Version110Transformer();
        }

        // select(*)
        // $this->addSelect();
        $this->addFilters();

        $this->filtered = $this->crud->count();

        $this->addOrderBy();
        $this->addLimits();

        $this->rows = $this->builder->get();

        $rows = [];

        foreach ($this->rows as $row) {
            $rows[] = $this->formatRow($row);
        }

        return [
            static::$versionTransformer->transform('draw') => (isset($_POST[static::$versionTransformer->transform(
                    'draw'
                )]) ? (int) $_POST[static::$versionTransformer->transform('draw')] : 0),
            static::$versionTransformer->transform('recordsTotal') => $this->total,
            static::$versionTransformer->transform('recordsFiltered') => $this->filtered,
            static::$versionTransformer->transform('data') => $rows,
        ];
    }

    /**
     * @param $data
     *
     * @return array|mixed
     */
    private function formatRow($data)
    {
        // if we have a custom format row function we trigger it instead of the default handling.
        if (null !== $this->formatRowFunction) {
            $function = $this->formatRowFunction;

            return call_user_func($function, $data);
        }

        $result = [];

        foreach ($this->columnNames as $column) {
            $result[] = $data[$column];
        }
        $data = $result;

        $data = $this->formatRowIndexes($data);

        return $data;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function formatRowIndexes($data)
    {
        if (isset($data['id'])) {
            $data[static::$versionTransformer->transform('DT_RowId')] = $data['id'];
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getColumnNames()
    {
        $names = [];

        foreach ($this->columns as $index => $column) {
            if ($column instanceof ExpressionWithName) {
                $names[] = $column->getName();

                continue;
            }

            if (is_string($column) && strstr($column, '.')) {
                $column = explode('.', $column);
            }

            $names[] = (is_array($column) ? $this->arrayToCamelcase($column) : $column);
        }

        return $names;
    }

    /**
     * @param $columns
     *
     * @return array
     */
    private function getRawColumns($columns)
    {
        $rawColumns = [];

        foreach ($columns as $column) {
            $rawColumns[] = $this->getRawColumnQuery($column);
        }

        return $rawColumns;
    }

    /**
     * @param $column
     *
     * @return raw|string
     */
    private function getRawColumnQuery($column)
    {
        if ($column instanceof ExpressionWithName) {
            return $column->getExpression();
        }

        if (is_array($column)) {
            if ('sqlite' == $this->getDatabaseDriver()) {
                return '('.implode(' || " " || ', $this->getRawColumns($column)).')';
            }

            return 'CONCAT('.implode(', " ", ', $this->getRawColumns($column)).')';
        }

        return Model::resolveConnection()->getQueryGrammar()->wrap($column);
    }

    /**
     * @return string
     */
    private function getDatabaseDriver()
    {
        return Model::resolveConnection()->getDriverName();
    }

    private function addSelect()
    {
        $rawSelect = [];

        foreach ($this->columns as $index => $column) {
            if (isset($this->rawColumns[$index])) {
                $rawSelect[] = $this->rawColumns[$index].' as '.Model::resolveConnection()->getQueryGrammar()->wrap($this->columnNames[$index]);
            }
        }
        $this->builder = $this->builder->select(new raw(implode(', ', $rawSelect)));
    }

    /**
     * @param $array
     * @param bool|false $inForeach
     *
     * @return array|string
     */
    private function arrayToCamelcase($array, $inForeach = false)
    {
        $result = [];

        foreach ($array as $value) {
            if (is_array($value)) {
                $result += $this->arrayToCamelcase($value, true);
            }
            $value = explode('.', $value);
            $value = end($value);
            $result[] = $value;
        }

        return !$inForeach ? camel_case(implode('_', $result)) : $result;
    }

    /**
     * Add the filters based on the search value given.
     *
     * @return $this
     */
    private function addFilters()
    {
        $search = static::$versionTransformer->getSearchValue();

        if ('' != $search) {
            $this->addAllFilter($search);
        }
        $this->addColumnFilters();

        return $this;
    }

    /**
     * Searches in all the columns.
     *
     * @param $search
     */
    private function addAllFilter($search)
    {
        $this->builder = $this->builder->where(
            function ($query) use ($search) {
                foreach ($this->columns as $column) {
                    $searchable = !isset($this->searchableColumns) || isset($this->searchableColumns[$column->getName()]);

                    if ($searchable) {
                        $query->orWhere(
                            $this->getRawColumnQuery($column),
                            'ilike',
                            '%'.$search.'%'
                        );
                    }
                }
            }
        );
    }

    /**
     * Add column specific filters.
     */
    private function addColumnFilters()
    {
        foreach ($this->columns as $i => $column) {
            if (static::$versionTransformer->isColumnSearched($i)) {
                $this->builder->where(
                    new raw($this->getRawColumnQuery($column)),
                    'like',
                    '%'.static::$versionTransformer->getColumnSearchValue($i).'%'
                );
            }
        }
    }

    /**
     * Depending on the sorted column this will add orderBy to the builder.
     */
    protected function addOrderBy()
    {
        if (static::$versionTransformer->isOrdered()) {
            foreach (static::$versionTransformer->getOrderedColumns() as $index => $direction) {
                if (isset($this->columnNames[$index])) {
                    $this->builder->orderBy(
                        $this->columnNames[$index],
                        $direction
                    );
                }
            }
        } elseif (is_array($this->orderBy)) {
            foreach ($this->orderBy as $column => $direction) {
                $this->builder->orderBy($column, $direction);
            }
        }
    }

    /**
     * Adds the pagination limits to the builder.
     */
    private function addLimits()
    {
        if (isset($_POST[static::$versionTransformer->transform(
                    'start'
                )]) && '-1' != $_POST[static::$versionTransformer->transform('length')]
        ) {
            $this->builder->skip((int) $_POST[static::$versionTransformer->transform('start')])->take(
                (int) $_POST[static::$versionTransformer->transform('length')]
            );
        }
    }
}
