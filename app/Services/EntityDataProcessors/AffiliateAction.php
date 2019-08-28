<?php

namespace App\Services\EntityDataProcessors;

class AffiliateAction extends DataProcessorAbstract implements HasTargetActionId
{
    public function couldBeAutoConfirmed(): bool
    {
        return $this->entity->data->isAffiliateRewardsPaidToUs;
    }

    public function getAffiliateRewardAmount(): float
    {
        return (float) $this->entity->data->affiliateRewardAmount;
    }

    public function getTargetActionId(): ?int
    {
        return (int) $this->entity->data->crmActionId;
    }
}
