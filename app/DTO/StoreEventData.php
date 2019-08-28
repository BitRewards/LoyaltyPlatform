<?php

namespace App\DTO;

use App\Models\StoreEvent;

class StoreEventData
{
    const DATA_KEY_TARGET_ACTION_ID = '__target_action_id__';
    const DATA_KEY_SOURCE_CODE_ID = 'source_code_id';
    const DATA_KEY_TRANSACTION_COMMENT = 'transaction_comment';
    const DATA_KEY_TRANSACTION_TAG = 'transaction_tag';

    const DATA_KEY_TREASURY_TX_HASH = 'treasury_tx_hash';
    const DATA_KEY_TREASURY_DATA = 'treasury_data';
    const DATA_KEY_TREASURY_SENDER_ADDRESS = 'treasury_sender_address';
    const DATA_KEY_TREASURY_ETH_AMOUNT = 'treasury_eth_amount';

    const DATA_KEY_PARENT_TRANSACTION_ID = 'parent_transaction_id';

    const DATA_KEY_SHARED_ON_URL = 'shared_on_url';

    public function __construct($action, array $data = [], $entityType = null, $entityExternalId = null, $converterType = null, $actorId = null)
    {
        $this->action = $action;
        $this->data = $data;
        $this->entityType = $entityType;
        $this->entityExternalId = $entityExternalId;
        $this->converterType = $converterType;
        $this->actorId = $actorId;
    }

    public $action;
    public $entityType;
    public $entityExternalId;
    public $actorId;

    public $data;
    public $converterType;

    public static function makeConfirmation($entityType, $entityId, $initiator = null)
    {
        return new static(
            StoreEvent::ACTION_CONFIRM,
            $initiator ? ['initiator' => $initiator] : [],
            $entityType,
            $entityId
        );
    }

    public static function makeRejection($entityType, $entityId, $initiator = null)
    {
        return new static(
            StoreEvent::ACTION_REJECT,
            $initiator ? ['initiator' => $initiator] : [],
            $entityType,
            $entityId
        );
    }
}
