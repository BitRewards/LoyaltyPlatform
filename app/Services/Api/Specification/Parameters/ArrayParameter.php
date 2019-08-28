<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\ArrayTrait;

abstract class ArrayParameter extends Parameter
{
    use ArrayTrait;

    /**
     * Indicates that current parameter accepts list of unique value in CSV format.
     *
     * @param string $itemsType Each item type (string, int32, int64, etc.).
     *
     * @return static
     */
    public function asUniqueCsv(string $itemsType)
    {
        return $this->asCsv($itemsType)->attachData('uniqueItems', true);
    }

    /**
     * Indicates that current parameter accepts list of values in CSV format.
     *
     * @param string $itemsType Each item type (string, int32, int64, etc.).
     *
     * @return static
     */
    public function asCsv(string $itemsType)
    {
        return $this->attachData('collectionFormat', 'csv')
            ->attachData('items.type', $itemsType);
    }

    /**
     * Set the array items type.
     *
     * @param string $type
     *
     * @return static
     */
    public function usingItemsType(string $type)
    {
        return $this->attachData('items.type', $type);
    }
}
