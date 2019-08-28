<?php

namespace App\Services;

use App\Jobs\AutoFinishEntity;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\DTO\StoreEventData;
use App\Models\Transaction;
use Carbon\Carbon;

class StoreEntityService
{
    public function createOrUpdateEntity(StoreEvent $event)
    {
        $entity = $this->getEntityByEvent($event);

        if (!$entity) {
            $couldBeAutoConfirmedBeforeUpdate = false;

            \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: creating entity...");
            $entity = new StoreEntity();
            $entity->external_id = $event->entity_external_id;
            $entity->type = $event->entity_type;
            $entity->confirmed_at = null;
            $entity->partner_id = $event->partner_id;
            $entity->data = $event->data;
            $entity->status = StoreEntity::STATUS_PENDING;
        } else {
            $couldBeAutoConfirmedBeforeUpdate = $entity->getDataProcessor()->couldBeAutoConfirmed();

            $lastProcessedEventCreatedAt = $entity->lastProcessedStoreEvent->external_event_created_at ?? null;

            // skip entity processing if external timestamp goes before stored one
            if ($lastProcessedEventCreatedAt && $event->external_event_created_at && $event->external_event_created_at->lt($lastProcessedEventCreatedAt)) {
                return $entity;
            }

            \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: found entity {$entity->id}...");
            $entity->data = array_replace_recursive($entity->data->toArray(), $event->data->toArray());
        }

        $entity->last_processed_store_event_id = $event->id;

        $couldBeAutoConfirmedAfterUpdate = $entity->getDataProcessor()->couldBeAutoConfirmed();

        $shouldAutoConfirmLogicWork =
            $couldBeAutoConfirmedAfterUpdate &&
            !$couldBeAutoConfirmedBeforeUpdate;

        if ($shouldAutoConfirmLogicWork) {
            if ($timestamp = $entity->data->statusAutoFinishesAt) {
                $entity->status_auto_finishes_at = Carbon::parse($entity->data->statusAutoFinishesAt);
            } elseif (null !== ($interval = $this->getStatusAutoFinishInterval($entity))) {
                $entity->status_auto_finishes_at = Carbon::now()->addSeconds($interval);
            }
        }

        \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: before saving entity {$entity->id}...");
        $entity->save();
        \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: saved entity {$entity->id}...");

        if (isset($interval) && 0 == $interval && !$entity->isConfirmed() && $couldBeAutoConfirmedAfterUpdate) {
            \HMisc::echoIfDebuggingInConsole("Handling event {$event->id}: confirming entity {$entity->id}...");
            $this->confirmEntity($entity, 'zero-auto-confirm-interval');
        } else {
            if ($entity->status_auto_finishes_at && $entity->isPending()) {
                if (Carbon::now()->diffInSeconds($entity->status_auto_finishes_at) <= 3600) {
                    dispatch(
                        (new AutoFinishEntity($entity->id))->delay(
                            Carbon::now()->diffInSeconds($entity->status_auto_finishes_at)
                        )
                    );
                }
            }
        }

        return $entity;
    }

    public function confirmEntity(StoreEntity $entity, $initiator = null)
    {
        $confirmation = StoreEventData::makeConfirmation(
            $entity->type,
            $entity->external_id,
            $initiator
        );

        app(StoreEventService::class)->saveEvent($entity->partner, $confirmation);
    }

    /**
     * @param StoreEvent $event
     *
     * @return StoreEntity|null
     */
    public function getEntityByEvent(StoreEvent $event)
    {
        $entity = StoreEntity
            ::where('type', $event->entity_type)
            ->where('external_id', $event->entity_external_id)
            ->where('partner_id', $event->partner_id)
            ->first();

        return $entity;
    }

    public function getStatusAutoFinishInterval(StoreEntity $entity)
    {
        $partnerSetting = ($entity->partner->getEntityAutoFinishInterval($entity->type));

        if (null !== $partnerSetting) {
            if ($partnerSetting < 0) {
                return null;
            } else {
                return $partnerSetting;
            }
        }

        switch ($entity->type) {
            case StoreEntity::TYPE_ORDER:
                return 14 * 3600 * 24;

            case StoreEntity::TYPE_AFFILIATE_ACTION:
                return 0;

            case StoreEntity::TYPE_JOIN:
                return null;

            case StoreEntity::TYPE_REVIEW:
                return null;

            case StoreEntity::TYPE_SHARE:
                return null;

            default:
                return null;
        }
    }

    public function processAutoFinishingEntities()
    {
        $strangeTransactionsQuery = '
            SELECT source_store_entity_id FROM transactions AS t
            INNER JOIN store_entities AS se ON (se.id = t.source_store_entity_id)
            WHERE
            t.status = :pending AND  
            se.status = :confirmed 
        ';

        $entityIds = \DB::select(
            $strangeTransactionsQuery,
            ['pending' => Transaction::STATUS_PENDING, 'confirmed' => StoreEntity::STATUS_CONFIRMED]
        );
        $entityIds = array_pluck($entityIds, 'source_store_entity_id');

        $entities = StoreEntity
            ::where('status_auto_finishes_at', '<=', Carbon::now())
            ->where('status', StoreEntity::STATUS_PENDING)
            ->whereNotNull('external_id')
            ->orWhereIn('id', $entityIds)
            ->with('partner')
            ->get();

        /*
         * @var StoreEntity[]
         */
        foreach ($entities as $entity) {
            $this->autoFinishEntity($entity);
        }
    }

    public function autoFinishEntity(StoreEntity $entity)
    {
        if (!($entity->status_auto_finishes_at && $entity->status_auto_finishes_at->subSeconds(5)->isPast())) {
            return;
        }

        if ($entity->getDataProcessor()->couldBeAutoConfirmed()) {
            $eventData = StoreEventData::makeConfirmation($entity->type, $entity->external_id, 'auto-finish-time');
        } else {
            $eventData = StoreEventData::makeRejection($entity->type, $entity->external_id, 'auto-finish-time');
        }

        app(StoreEventService::class)->saveAndHandle($entity->partner, $eventData);
    }
}
