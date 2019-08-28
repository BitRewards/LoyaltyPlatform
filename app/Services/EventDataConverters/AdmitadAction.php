<?php

namespace App\Services\EventDataConverters;

use App\DTO\AdmitadActionData;
use App\Services\AdmitadService;
use Carbon\Carbon;

class AdmitadAction extends Base
{
    public function affiliateRewardAmount()
    {
        return $this->data['payment'] ?? null;
    }

    public function getOriginalOrderId()
    {
        return $this->data['order_id'] ?? null;
    }

    public function isAffiliateRewardsPaidToUs(): bool
    {
        $status = $this->data['status'] ?? null;

        return 'approved' === $status && !empty($this->data['processed']) && !empty($this->data['paid']);
    }

    public function getEntityExternalId()
    {
        return app(AdmitadService::class)->getEntityExternalIdFromRawData($this->data);
    }

    public function getConvertedData(): AdmitadActionData
    {
        return AdmitadActionData::make([
            'email' => $this->data['subid4'] ?? null,
            'userCrmKey' => $this->data['subid1'] ?? null,
            'affiliateRewardAmount' => $this->affiliateRewardAmount(),
            'originalOrderId' => $this->getOriginalOrderId(),
            'isAffiliateRewardsPaidToUs' => $this->isAffiliateRewardsPaidToUs(),
            'crmActionId' => $this->data['subid2'] ?? null,
        ]);
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        if (isset($this->data['status_updated'])) {
            return Carbon::parse($this->data['status_updated']);
        }

        return null;
    }
}
