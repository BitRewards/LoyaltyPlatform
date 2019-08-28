<?php

namespace App\Services\Api\Definitions;

interface ApiDefinitionInterface
{
    /**
     * Get defintion name.
     *
     * @return string
     */
    public function name();

    /**
     * Get the array representation of defintion.
     *
     * @return array
     */
    public function toArray();
}
