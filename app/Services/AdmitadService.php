<?php

namespace App\Services;

use Admitad\Api\Api;
use App\DTO\StoreEventData;
use App\Models\Partner;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\EventDataConverters\AdmitadAction;

class AdmitadService
{
    /**
     * @var Api
     */
    protected $client;

    /**
     * @var AdmitadAction
     */
    protected $eventDataConverter;

    /**
     * @var StoreEventService
     */
    protected $storeEventService;

    public function __construct(
        Api $client,
        StoreEventService $storeEventService
    ) {
        $this->client = $client;
        $this->storeEventService = $storeEventService;
    }

    public function sync(Partner $partner, \DateInterval $statisticInterval, bool $skipDuplicates = true)
    {
        /** @var StoreEventData $eventData */
        foreach ($this->getStatisticEvents($statisticInterval) as $eventData) {
            \HMisc::echoIfDebuggingInConsole('Processing event', $eventData->data);

            if ($skipDuplicates && $this->isDuplicatedEvent($partner, $eventData)) {
                \HMisc::echoIfDebuggingInConsole('Skipping duplicated event...');

                continue;
            }

            $this->storeEventService->saveEvent($partner, $eventData, true);
        }
    }

    public function getStatisticEvents(\DateInterval $statisticInterval): \Iterator
    {
        $resultIterator = $this->client->getIterator('/statistics/actions/', [
            'date_start' => (new \DateTime())->sub($statisticInterval)->format('d.m.Y'),
        ]);

        /** @var \ArrayObject $result */
        foreach ($resultIterator as $result) {
            $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
            $eventData->entityType = StoreEntity::TYPE_AFFILIATE_ACTION;
            $eventData->data = json_decode(json_encode($result), true);
            $eventData->converterType = StoreEvent::CONVERTER_TYPE_ADMITAD_ACTION;

            yield $eventData;
        }
    }

    public function getEntityExternalIdFromRawData(array $rawData)
    {
        return isset($rawData['action_id']) ? "ad-{$rawData['action_id']}" : null;
    }

    public function isDuplicatedEvent(Partner $partner, StoreEventData $storeEventData): bool
    {
        $entityExternalId = $this->getEntityExternalIdFromRawData($storeEventData->data);
        $entity = $this->storeEventService->getLastEventForEntity(
            $partner,
            StoreEntity::TYPE_AFFILIATE_ACTION,
            $entityExternalId
        );

        return $entity && ($entity->raw_data == $storeEventData->data);
    }
}
