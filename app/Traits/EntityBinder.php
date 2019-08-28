<?php

namespace App\Traits;

trait EntityBinder
{
    public function show($entity)
    {
        return parent::show($entity->id);
    }

    public function edit($entity)
    {
        return parent::edit($entity->id);
    }

    public function destroy($entity)
    {
        return parent::destroy($entity->id);
    }
}
