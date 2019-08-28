<?php

namespace App\Traits;

trait EntityCopy
{
    public function copy($entity)
    {
        $this->crud->hasAccessOrFail('update');

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($entity->id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getUpdateFields($entity->id);
        $this->data['title'] = trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $entity->id;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::copy', $this->data);
    }
}
