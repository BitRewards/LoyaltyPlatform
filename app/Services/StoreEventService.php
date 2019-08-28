<?php

namespace App\Services;

use App\Exceptions\MalformedRawEventDataException;
use App\Exceptions\StoreEventProcessingException;
use App\Jobs\HandleStoreEvent;
use App\Models\Action;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Models\Partner;
use App\DTO\StoreEventData;
use App\Services\ActionProcessors\Base;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Lock\Factory;

class StoreEventService
{
    /**
     * @var Factory
     */
    protected $lockFactory;

    public function __construct(Factory $lockFactory)
    {
        $this->lockFactory = $lockFactory;
    }

    public function saveAndHandle(Partner $partner, StoreEventData $data)
    {
        return $this->saveEvent($partner, $data, true);
    }

    /**
     * @param Partner        $partner
     * @param StoreEventData $eventData
     * @param bool           $handleImmediately
     *
     * @return StoreEvent
     */
    public function saveEvent(Partner $partner, StoreEventData $eventData, $handleImmediately = false)
    {
        $event = new StoreEvent();
        $event->action = $eventData->action;
        $event->entity_external_id = $eventData->entityExternalId;
        $event->entity_type = $eventData->entityType;
        $event->raw_data = $eventData->data;
        $event->partner_id = $partner->id;
        $event->actor_id = intval($eventData->actorId);
        $event->converter_type = $eventData->converterType;
        $event->save();

        if ($handleImmediately) {
            $this->handleEvent($event);
        } else {
            dispatch(new HandleStoreEvent($event->id));
        }

        return $event;
    }

    /**
     * @param \App\Models\StoreEvent $event
     *
     * @throws \Throwable
     */
    public function handleEvent(StoreEvent $event)
    {
        \HMisc::echoIfDebuggingInConsole("Starting transaction for event {$event->id}...");

        \DB::transaction(function () use ($event) {
            $lockKeyEvent = "processing-event-{$event->id}";
            $lockTTL = config('database.redis.redis_lock.ttl');

            $processingEventLock = $this->lockFactory->createLock($lockKeyEvent, $lockTTL);

            \HMisc::echoIfDebuggingInConsole("Acquiring redis lock {$lockKeyEvent}...");

            if ($processingEventLock->acquire(true)) {
                $event->refresh();

                if ($event->processed_at) {
                    return;
                }

                \HMisc::echoIfDebuggingInConsole("Acquired pgsql lock on event {$event->id}!");

                $this->doDataTransformation($event);

                $lockKey =
                    ($event->entity_external_id && $event->entity_type) ?
                        ('processing-event-store-entity-'.$event->entity_type.'-'.$event->entity_external_id) :
                        ('processing-event-partner-'.$event->partner_id);

                $lock = $this->lockFactory->createLock($lockKey, $lockTTL);

                \HMisc::echoIfDebuggingInConsole("Acquiring redis lock on key {$lockKey}...");

                if ($lock->acquire(true)) {
                    $this->doInitialProcessing($event);

                    try {
                        \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: before ActionService::processEvent...");

                        $this->runActionProcessors($event);

                        $event->processed_at = Carbon::now();
                        $event->save();

                        \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: after ActionService::processEvent...");
                    } catch (\Throwable $e) {
                        \HMisc::echoIfDebuggingInConsole(
                            '!StoreEventService::handleEvent, entity processing trouble!', [$e, $event]);

                        throw $e;
                    }

                    \HMisc::echoIfDebuggingInConsole("Acquired redis lock on key {$lockKey}!");
                } else {
                    \HMisc::echoIfDebuggingInConsole("Failed to acquire redis lock on key {$lockKey} :(");

                    throw new StoreEventProcessingException('Failed to obtain lock');
                }
            }
        });
    }

    private function doDataTransformation(StoreEvent $event)
    {
        if ($converter = $event->getConverter()) {
            try {
                $event->data = $converter->getConvertedData();
            } catch (MalformedRawEventDataException $e) {
                $event->processed_at = Carbon::now();
                $event->save();

                return;
            }

            if ($externalId = $converter->getEntityExternalId()) {
                $event->entity_external_id = $externalId;
            }

            $event->raw_data = $converter->getRawData();

            $externalEventCreatedAt = $converter->getExternalEventCreatedAt();

            if ($externalEventCreatedAt) {
                $event->external_event_created_at = $externalEventCreatedAt;
            }
        } else {
            $event->data = $event->raw_data;
        }

        \HMisc::echoIfDebuggingInConsole("Saving event {$event->id} after transforming...");

        $event->save();

        \HMisc::echoIfDebuggingInConsole("Saved event {$event->id} after transforming!");
    }

    private function doInitialProcessing(StoreEvent $event)
    {
        switch ($event->action) {
            case StoreEvent::ACTION_STORE:
                $entity = app(StoreEntityService::class)->createOrUpdateEntity($event);

                break;

            case StoreEvent::ACTION_CONFIRM:
                $entity = app(StoreEntityService::class)->getEntityByEvent($event);

                if (!$entity) {
                    throw new HttpException(404);
                }
                $entity->confirmed_at = $entity->confirmed_at ?: Carbon::now();
                // force null on other property to correctly update previously rejected entity
                $entity->rejected_at = null;
                $entity->status = StoreEntity::STATUS_CONFIRMED;
                $entity->save();

                break;

            case StoreEvent::ACTION_REJECT:
                $entity = app(StoreEntityService::class)->getEntityByEvent($event);

                if (!$entity) {
                    throw new HttpException(404);
                }

                // force null on other property to correctly update previously confirmed entity
                $entity->confirmed_at = null;
                $entity->rejected_at = Carbon::now();
                $entity->status = StoreEntity::STATUS_REJECTED;
                $entity->save();

                break;

            default:
                break;
        }

        if (isset($entity)) {
            $event->store_entity_id = $entity->id;
            $event->save();
        }
    }

    private function runActionProcessors(StoreEvent $event)
    {
        $actions = Action::model()->whereAttributes([
            'partner_id' => $event->partner_id,
            'status' => Action::STATUS_ENABLED,
        ])->orderBy('id')->get();

        foreach ($actions as $action) {
            $processor = $action->getActionProcessor();
            /*
             * @var Base
             */
            if ($processor->canHandle($event)) {
                $processor->handle($event);

                if (!$this->canBeHandledMultipleTimes($event->action)) {
                    break;
                }
            }
        }
    }

    private function canBeHandledMultipleTimes($actionType)
    {
        return StoreEvent::ACTION_CONFIRM == $actionType || StoreEvent::ACTION_STORE == $actionType;
    }

    public function getLastEventForEntity(Partner $partner, string $entityType, string $entityExternalId)
    {
        return StoreEvent::model()
            ->whereAttributes([
                'entity_type' => $entityType,
                'entity_external_id' => $entityExternalId,
                'partner_id' => $partner->id,
            ])
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();
    }

    public function getUnprocessedAffiliateActionEvents(): Collection
    {
        $sql = <<<SQL
SELECT
       store_events.*
FROM
     store_entities
LEFT JOIN transactions ON transactions.source_store_entity_id = store_entities.id
INNER JOIN store_events ON store_entities.last_processed_store_event_id = store_events.id
WHERE
    store_entities.type = ?
AND
  transactions.id IS NULL
SQL;
        $result = \DB::select($sql, [StoreEntity::TYPE_AFFILIATE_ACTION]);

        return StoreEvent::hydrate($result);
    }
}
