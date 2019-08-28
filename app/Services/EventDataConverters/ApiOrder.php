<?php

namespace App\Services\EventDataConverters;

use App\DTO\OrderData;

class ApiOrder extends Base
{
    public function getEntityExternalId()
    {
        return $this->data['id'] ?? null;
    }

    public function getConvertedData(): OrderData
    {
        $fields = [
            'email', 'phone', 'user_crm_key',
            'ref_user_crm_key', 'status_autofinishes_at',
            'name', 'amount_total', 'is_paid', 'is_delivered',
            'comment', 'manager_comment', 'promo_codes',
            'amount_total_discounted', 'public_id',
            'predefined_cashback', 'predefined_referrer_cashback',
        ];

        $data = [];

        if ($this->data['promocodes'] ?? null) {
            // hotfix
            $data['promoCodes'] = $this->data['promocodes'];
        }

        foreach ($fields as $field) {
            if ($this->data[$field] ?? null) {
                $camelCaseField = camel_case($field);
                $data[$camelCaseField] = $this->data[$field];
            }
        }

        $singlePromocode =
            $this->data['promocode'] ??
            ($this->data['promo_code'] ??
                ($this->data['promoCode'] ?? null));

        if ($singlePromocode) {
            if (!($data['promoCodes'] ?? null)) {
                $data['promoCodes'] = [];
            }

            if (!is_array($data['promoCodes'])) {
                $data['promoCodes'] = [];
            }

            $data['promoCodes'][] = $singlePromocode;
            $data['promoCodes'] = array_unique($data['promoCodes']);
        }

        // Cast pseudo-boolean fields to boolean.

        foreach (['isPaid', 'isDelivered'] as $booleanField) {
            if (isset($data[$booleanField])) {
                $data[$booleanField] = (bool) intval($data[$booleanField]);
            }
        }

        return OrderData::make($data);
    }
}
