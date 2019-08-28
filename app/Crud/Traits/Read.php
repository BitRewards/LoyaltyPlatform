<?php

namespace App\Crud\Traits;

trait Read
{
    use \Backpack\CRUD\PanelTraits\Read;

    /**
     * Overriding the parent trait method;.
     *
     * @return [Number] The id in the db or false
     */
    public function getCurrentEntryId()
    {
        if ($this->entry) {
            return $this->entry->getKey();
        }

        $params = \Route::current()->parameters();

        if (isset($this->request->{$this->entity_name})) {
            $id = $this->request->{$this->entity_name};

            return is_scalar($id) ? $id : $id->getKey();
        } else {
            $id = array_values($params)[count($params) - 1] ?? null;

            if ($id) {
                return is_scalar($id) ? $id : $id->getKey();
            } else {
                return false;
            }
        }
    }
}
