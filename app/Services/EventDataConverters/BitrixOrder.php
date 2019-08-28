<?php

namespace App\Services\EventDataConverters;

use App\DTO\OrderData;
use App\Models\Partner;
use Carbon\Carbon;

class BitrixOrder extends Base
{
    public function getAmountTotal()
    {
        $order = $this->data;
        $totalAmount = $order['PRICE'] ?? null;

        if (isset($order['PRICE_DELIVERY'])) {
            $totalAmount -= $order['PRICE_DELIVERY'];
        }

        return $totalAmount;
    }

    private function hasGoodStatus(): bool
    {
        $order = $this->data;
        $status = $order['STATUS_ID'] ?? null;
        $goodStatuses = ['F', 'P'];

        if ($this->getStoreEvent()->partner->getSetting(Partner::SETTINGS_BITRIX_ALLOW_DF_STATUS)) {
            $goodStatuses[] = 'DF';
        }

        return \in_array($status, $goodStatuses, true);
    }

    private function isDelivered(): bool
    {
        return $this->hasGoodStatus();
    }

    private function isPaid(): bool
    {
        return $this->hasGoodStatus();
    }

    public function getEntityExternalId()
    {
        return $this->data['ID'] ?? null;
    }

    public function getConvertedData(): OrderData
    {
        $nameParts = [];

        if (isset($this->data['USER']['NAME'])) {
            $nameParts[] = trim($this->data['USER']['NAME']);
        } elseif (isset($this->data['USER']['USER_NAME'])) {
            $nameParts[] = trim($this->data['USER']['USER_NAME']);
        }

        if (isset($this->data['USER']['LAST_NAME'])) {
            $nameParts[] = trim($this->data['USER']['LAST_NAME']);
        } elseif (isset($this->data['USER']['USER_LAST_NAME'])) {
            $nameParts[] = trim($this->data['USER']['USER_LAST_NAME']);
        }

        $name = trim(implode(' ', $nameParts)) ?: null;

        $properties = $this->data['PROPERTIES'] ?? [];

        $realEmail = $realName = $realPhone = null;

        foreach ($properties as $property) {
            if (!isset($property['VALUE'])) {
                continue;
            }

            if ('FIO' === ($property['CODE'] ?? null) || 'Y' === ($property['IS_PAYER'] ?? null)) {
                $realName = $property['VALUE'];
            }

            if ('EMAIL' === ($property['CODE'] ?? null) || 'Y' === ($property['IS_EMAIL'] ?? null)) {
                $realEmail = $property['VALUE'];
            }

            if ('phone' === strtolower($property['CODE'] ?? null)
                || false !== mb_strpos($property['NAME'] ?? '', 'Телефон')
            ) {
                $realPhone = $property['VALUE'];
            }
        }

        $promoCode = $this->data['PROMO_CODE'] ?? null;
        $promoCodesArray = $promoCode ? [$promoCode] : [];

        return OrderData::make([
            'amountTotal' => $this->getAmountTotal(),
            'isPaid' => $this->isPaid(),
            'isDelivered' => $this->isDelivered(),
            'userCrmKey' => $this->data['COOKIES']['gcrm_user_key'] ?? null,
            'refUserCrmKey' => $this->data['COOKIES']['gcrm_ref_user_key'] ?? null,
            'managerComment' => $this->data['COMMENTS'] ?? null,
            'comment' => $this->data['USER_DESCRIPTION'] ?? null,
            'email' => $realEmail ?: ($this->data['USER']['USER_EMAIL'] ?? null),
            'name' => $realName ?: $name,
            'phone' => $realPhone ?: ($this->data['USER']['PERSONAL_PHONE'] ?? null),
            'promoCodes' => $promoCodesArray,
        ]);
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        if (isset($this->data['DATE_UPDATE'])) {
            return Carbon::parse($this->data['DATE_UPDATE']);
        }

        return null;
    }
}
