<?php

namespace App\Services\EntityDataProcessors;

use App\Models\StoreEntity;

abstract class DataProcessorAbstract
{
    /**
     * @var StoreEntity
     */
    protected $entity;

    public function __construct(StoreEntity $entity)
    {
        $this->entity = $entity;
    }

    abstract public function couldBeAutoConfirmed(): bool;
}
