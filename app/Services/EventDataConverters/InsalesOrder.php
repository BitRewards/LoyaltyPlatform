<?php

namespace App\Services\EventDataConverters;

use App\DTO\OrderData;
use Carbon\Carbon;

class InsalesOrder extends Base
{
    private function getAmountTotal()
    {
        $orderData = $this->data;
        $amountTotalGiven = iso($orderData['items-price'], iso($orderData['items_price']));
        $amountTotalCalculated = 0;
        $orderLines = (array) iso($orderData['order-lines'], iso($orderData['order_lines']));
        $orderLines = (array) iso($orderLines['order-line'], iso($orderLines['order_line'])) ?: $orderLines;

        if (\count($orderLines) > 0 && !isset($orderLines[0])) {
            $orderLines = [$orderLines];
        }

        foreach ($orderLines as $orderLine) {
            $amountTotalCalculated += iso($orderLine['full_sale_price'], iso($orderLine['total_price'], iso($orderLine['total-price'])));
        }

        return max($amountTotalCalculated, $amountTotalGiven);
    }

    private function isDelivered(): bool
    {
        $fulfillmentStatus = iso($this->data['fulfillment-status'], iso($this->data['fulfillment_status']));

        return \in_array($fulfillmentStatus, ['delivered', 'dispatched', 'approved', 'accepted']);
    }

    private function isPaid(): bool
    {
        $financialStatus = iso($this->data['financial-status'], iso($this->data['financial_status']));

        return 'paid' === $financialStatus;
    }

    public function getEntityExternalId()
    {
        return $this->data['id'] ?? null;
    }

    public function getConvertedData(): OrderData
    {
        $promoCodes = [];

        foreach ($this->data['discounts'] ?? [] as $discountRow) {
            $promoCodeRegex = "/[\d\-]{6,}/iu"; //only digits and hyphens, min 6 chars
            if (preg_match($promoCodeRegex, $discountRow['description'] ?? '', $matches)) {
                $promoCodes[] = $matches[0];
            }
        }

        $promoCodes = array_unique($promoCodes);

        $orderLines = [];

        foreach ($this->data['order_lines'] ?? [] as $orderLine) {
            if (!isset($orderLine['product_id'])) {
                continue;
            }
            $orderLines[] = [
                'total_price' => $orderLine['total_price'],
                'product_id' => $orderLine['product_id'],
                'quantity' => $orderLine['quantity'],
                'title' => $orderLine['title'] ?? null,
            ];
        }

        return OrderData::make([
            'amountTotal' => $this->getAmountTotal(),
            'isPaid' => $this->isPaid(),
            'isDelivered' => $this->isDelivered(),
            'userCrmKey' => $this->data['cookies']['gcrm_user_key'] ?? null,
            'refUserCrmKey' => $this->data['cookies']['gcrm_ref_user_key'] ?? null,
            'managerComment' => $this->data['manager_comment'] ?? null,
            'comment' => $this->data['comment'] ?? null,
            'email' => $this->data['client']['email'] ?? null,
            'phone' => $this->data['client']['phone'] ?? null,
            'name' => $this->data['client']['name'] ?? null,
            'promoCodes' => $promoCodes,
            'orderLines' => $orderLines,
        ]);
    }

    /**
     * Gets InSales internal order number for admin needs.
     *
     * @return string|null
     */
    public function getInsalesOrderNumber(): ?string
    {
        return $this->data['number'] ?? null;
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        if (isset($this->data['updated_at'])) {
            // order updated_at is equal to the insales webhook generation time
            return Carbon::parse($this->data['updated_at']);
        }

        return null;
    }

    public function getOriginalOrderId()
    {
        return $this->getInsalesOrderNumber();
    }
}
