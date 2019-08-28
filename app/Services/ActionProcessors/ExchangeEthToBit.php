<?php

namespace App\Services\ActionProcessors;

use App\DTO\StoreEventData;
use App\DTO\TransactionData;
use App\Models\StoreEvent;
use App\Models\Transaction;

class ExchangeEthToBit extends Base
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_EXCHANGE_ETH_TO_BIT;
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

        if (!empty($event->data[StoreEventData::DATA_KEY_TREASURY_TX_HASH])) {
            $data[Transaction::DATA_TREASURY_TX_HASH] = $event->data[StoreEventData::DATA_KEY_TREASURY_TX_HASH];
        }

        if (!empty($event->data[StoreEventData::DATA_KEY_TREASURY_SENDER_ADDRESS])) {
            $data[Transaction::DATA_TREASURY_SENDER_ADDRESS] = $event->data[StoreEventData::DATA_KEY_TREASURY_SENDER_ADDRESS];
        }

        return array_replace_recursive($currentData->toArray(), $data);
    }
}
