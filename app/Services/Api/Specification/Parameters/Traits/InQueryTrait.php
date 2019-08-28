<?php

namespace App\Services\Api\Specification\Parameters\Traits;

trait InQueryTrait
{
    /**
     * {@inheritdoc}
     */
    public function in()
    {
        return 'query';
    }
}
