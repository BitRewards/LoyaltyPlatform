<?php

namespace App\Crud\Traits;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait EditableJsonFieldsCrudTrait.
 *
 * @mixin CrudTrait
 * @mixin Model
 */
trait EditableJsonFieldsCrudTrait
{
    use CrudTrait {
        withFakes as originalWithFakes;
    }

    public function addExtraFakes($columns = [])
    {
        foreach ($columns as $column => $fields) {
            if (!isset($this->attributes[$column])) {
                continue;
            }

            $column_contents = $this->{$column};

            if ($this->shouldDecodeFake($column)) {
                $column_contents = json_decode($column_contents);
            }

            if ((is_array($column_contents) || is_object($column_contents) || $column_contents instanceof \Traversable)) {
                foreach ($column_contents as $fake_field_name => $fake_field_value) {
                    if (in_array($fake_field_name, $fields)) {
                        $this->setAttribute($fake_field_name, $fake_field_value);
                    }
                }
            }
        }
    }

    /**
     * Return the entity with fake fields as attributes.
     *
     * @param array $columns - the database columns that contain the JSONs
     *
     * @return Model
     */
    public function withFakes($columns = [])
    {
        $model = '\\'.get_class($this);

        if (!count($columns)) {
            $columns = (property_exists($model, 'fakeColumns')) ? $this->fakeColumns : ['extras'];
        }

        $this->addFakes($columns);

        $editableJsonFields = (property_exists($model, 'editableJsonFields')) ? $this->editableJsonFields : [];

        if (!empty($editableJsonFields)) {
            $this->addExtraFakes($editableJsonFields);
        }

        return $this;
    }
}
