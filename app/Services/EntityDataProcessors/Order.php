<?php

namespace App\Services\EntityDataProcessors;

class Order extends DataProcessorAbstract
{
    public function couldBeAutoConfirmed(): bool
    {
        if ($this->entity->partner->isDeliveredStatusEnoughForOrderAutoConfirm()) {
            return $this->isDelivered();
        }

        return $this->isPaid() && $this->isDelivered();
    }

    public function getAmountTotal(): ?float
    {
        return $this->entity->data->amountTotal ?? null;
    }

    public function isPaid(): bool
    {
        return $this->entity->data->isPaid ?? false;
    }

    public function isDelivered(): bool
    {
        return $this->entity->data->isDelivered ?? false;
    }

    /**
     * Determines if current order has any promo code attached.
     *
     * @return bool
     */
    public function hasPromoCodes(): bool
    {
        $promoCodes = $this->promoCodes();

        return \is_array($promoCodes) && \count($promoCodes) > 0;
    }

    /**
     * Get order promo codes list.
     *
     * @return array|null
     */
    public function promoCodes()
    {
        return $this->entity->data->promoCodes;
    }
}
