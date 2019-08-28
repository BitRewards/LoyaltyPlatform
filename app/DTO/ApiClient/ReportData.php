<?php

namespace App\DTO\ApiClient;

class ReportData
{
    /**
     * @var int
     */
    public $numberOfWorkingTools;

    /**
     * @var float
     */
    public $totalAmountOfPurchases;

    /**
     * @var float
     */
    public $averageOrderValue;

    /**
     * @var int
     */
    public $numberOfOrders;

    /**
     * @var int
     */
    public $numberOfIssuedPromoCodes;

    /**
     * @var int
     */
    public $numberOfUsedPromoCodes;

    /**
     * @var int
     */
    public $numberOfUniqueUsers;

    /**
     * @var float
     */
    public $averageChequeIncrease;

    /**
     * @var ToolReportData[]
     */
    public $tools = [];
}
