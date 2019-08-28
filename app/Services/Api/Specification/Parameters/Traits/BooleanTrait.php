<?php

namespace App\Services\Api\Specification\Parameters\Traits;

trait BooleanTrait
{
    /**
     * {@inheritdoc}
     */
    public function type()
    {
        return 'boolean';
    }
}
