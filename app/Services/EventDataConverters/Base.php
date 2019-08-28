<?php

namespace App\Services\EventDataConverters;

use App\Models\StoreEvent;
use Carbon\Carbon;

abstract class Base implements ConvertibleInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var StoreEvent
     */
    protected $storeEvent;

    public function __construct(StoreEvent $storeEvent)
    {
        $this->data = $storeEvent->raw_data ?? [];
        $this->storeEvent = $storeEvent;
    }

    public function getRawData(): array
    {
        return $this->data;
    }

    public function getStoreEvent(): StoreEvent
    {
        return $this->storeEvent;
    }

    public function getExternalEventCreatedAt(): ?Carbon
    {
        return null;
    }

    public function getOriginalOrderId()
    {
        return $this->getEntityExternalId();
    }
}
