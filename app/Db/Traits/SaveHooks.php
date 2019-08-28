<?php

namespace App\Db\Traits;

trait SaveHooks
{
    public function beforeSave()
    {
        // logic goes here
    }

    public function save(array $options = [])
    {
        $this->beforeSave();

        $result = parent::save($options);

        $this->afterSave();

        return $result;
    }

    public function afterSave()
    {
    }
}
