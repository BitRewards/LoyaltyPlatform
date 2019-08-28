<?php

namespace App\DTO\ApiClient;

class ToolReportData implements \JsonSerializable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $amountOfOrders;

    /**
     * @var float
     */
    public $averageOrderValue;

    /**
     * @var int
     */
    public $numberOfOrders;

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'amountOfOrders' => $this->amountOfOrders,
            'averageOrderValue' => $this->averageOrderValue,
            'numberOfOrders' => $this->numberOfOrders,
        ];
    }
}
