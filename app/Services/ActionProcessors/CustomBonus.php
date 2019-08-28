<?php

namespace App\Services\ActionProcessors;

use App\DTO\StoreEventData;
use App\DTO\TransactionData;
use App\Models\StoreEvent;
use App\Models\Transaction;

class CustomBonus extends Base
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_CUSTOM_BONUS;
    }

    public function getLimitPerUser()
    {
        return null;
    }

    public function getMinTimeBetween()
    {
        return null;
    }

    /**
     * @param StoreEvent      $event
     * @param TransactionData $currentData
     *
     * @return array
     */
    public function createTransactionData(StoreEvent $event, TransactionData $currentData = null)
    {
        $data = [];

        if (!empty($event->data[StoreEventData::DATA_KEY_TRANSACTION_COMMENT])) {
            $data[Transaction::DATA_COMMENT] = $event->data[StoreEventData::DATA_KEY_TRANSACTION_COMMENT];
        }

        if (!empty($event->data[StoreEventData::DATA_KEY_TRANSACTION_TAG])) {
            $data[Transaction::DATA_TAG] = $event->data[StoreEventData::DATA_KEY_TRANSACTION_TAG];
        }

        return array_replace_recursive($currentData->toArray(), $data);
    }
}
