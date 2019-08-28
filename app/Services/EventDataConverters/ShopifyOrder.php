<?php

namespace App\Services\EventDataConverters;

use App\DTO\OrderData;
use App\DTO\StoreEntityData;
use Carbon\Carbon;

class ShopifyOrder extends Base
{
    public function getAmountTotal()
    {
        return (float) $this->data['total_line_items_price'] ?? 0;
    }

    public function isDelivered()
    {
        $fulfillmentStatus = $this->data['fulfillment_status'] ?? null;

        return 'fulfilled' == $fulfillmentStatus;
    }

    public function isPaid()
    {
        $fulfillmentStatus = $this->data['financial_status'] ?? null;

        return 'paid' == $fulfillmentStatus;
    }

    public function getEntityExternalId()
    {
        return $this->data['id'] ?? null;
    }

    public function getConvertedData(): StoreEntityData
    {
        $landingUrl = $this->data['landing_site'] ?? null;
        preg_match('/gcrm_ref_user_key=([a-zA-Z0-9]+)/', $landingUrl, $matches);
        $refUserKey = $matches[1] ?? null;

        $managerCommentToOrder = $this->data['note'] ?? null;
        $managerCommentToCustomer = $this->data['customer']['note'] ?? null;
        $comments = array_filter([$managerCommentToOrder, $managerCommentToCustomer]);
        $managerComment = implode("\n\n", $comments);

        $customerName =
            $this->data['customer']['name'] ?? (
                $this->data['shipping_address']['name'] ?? (
                    $this->data['billing_address']['name'] ?? null
                )
            );

        $promoCodes = [];

        foreach ($orderData['discount_codes'] ?? [] as $discountCodeRow) {
            $promoCode = $discountCodeRow['code'] ?? null;

            if ($promoCode) {
                $promoCodes[] = $promoCode;
            }
        }

        return OrderData::make([
            'amountTotal' => $this->getAmountTotal(),
            'isPaid' => $this->isPaid(),
            'isDelivered' => $this->isDelivered(),
            'userCrmKey' => null,
            'refUserCrmKey' => $refUserKey,
            'managerComment' => $managerComment,
            'comment' => null,
            'email' => $this->data['email'] ?? null,
            'phone' => $this->data['phone'] ?? null,
            'name' => $customerName,
            'promoCodes' => $promoCodes,
        ]);
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        if (isset($this->data['updated_at'])) {
            return Carbon::parse($this->data['updated_at']);
        }

        return null;
    }
}
