<?php

namespace App\DTO\ApiClient;

class ReportDataFactory
{
    public function create(array $rawData): ReportData
    {
        $reportData = new ReportData();
        $reportData->numberOfWorkingTools = $rawData['total']['numberOfWorkingTools'] ?? 0;
        $reportData->totalAmountOfPurchases = (float) ($rawData['total']['totalAmountOfPurchases'] ?? 0);
        $reportData->averageOrderValue = (float) ($rawData['total']['averageOrderValue'] ?? 0);
        $reportData->numberOfOrders = $rawData['total']['numberOfOrders'] ?? 0;
        $reportData->numberOfIssuedPromoCodes = $rawData['total']['numberOfIssuedPromocodes'] ?? 0;
        $reportData->numberOfUsedPromoCodes = $rawData['total']['numberOfUsedPromocodes'] ?? 0;
        $reportData->numberOfUniqueUsers = $rawData['total']['numberOfUniqueUsers'] ?? 0;
        $reportData->averageChequeIncrease = isset($rawData['total']['averageCheckIncrease'])
            ? (float) $rawData['total']['averageCheckIncrease']
            : null;

        if (isset($rawData['tools']) && is_array($rawData['tools'])) {
            foreach ($rawData['tools'] as $toolData) {
                $toolReportData = new ToolReportData();
                $toolReportData->name = $toolData['name'] ?? '';
                $toolReportData->amountOfOrders = (float) ($toolData['amountOfOrders'] ?? 0);
                $toolReportData->averageOrderValue = (float) ($toolData['averageOrderValue'] ?? 0);
                $toolReportData->numberOfOrders = $toolData['numberOfOrders'] ?? 0;

                $reportData->tools[] = $toolReportData;
            }
        }

        return $reportData;
    }
}
