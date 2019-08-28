<?php

namespace App\Traits;

trait RelationFilter
{
    public function addRelationFilter($options)
    {
        $this->crud->addFilter([
            'name' => $options['name'],
            'type' => 'select2',
            'label' => $options['label'],
        ], function () use ($options) {
            $class = $options['class'];
            $key = $options['key'];
            $value = $options['value'];
            $values = [];

            foreach ($class::all() as $item) {
                $values[$item->$key] = $item->$value;
            }

            return $values;
        }, function ($key) use ($options) {
            $this->crud->addClause('where', $options['foreign_key'], $key);
        });
    }
}
