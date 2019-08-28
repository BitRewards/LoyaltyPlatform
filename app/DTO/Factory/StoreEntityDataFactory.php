<?php

namespace App\DTO\Factory;

use App\DTO\DTO;
use App\DTO\OrderData;
use App\DTO\ShareData;
use App\DTO\StoreEntityData;
use App\Models\StoreEntity;
use App\Models\StoreEvent;

class StoreEntityDataFactory
{
    private $dto;

    public function __construct($storeEventOrType)
    {
        if ($storeEventOrType instanceof StoreEvent) {
            $storeEventOrType = $storeEventOrType->entity_type;
        }

        if ($storeEventOrType instanceof StoreEntity) {
            $storeEventOrType = $storeEventOrType->type;
        }

        switch ($storeEventOrType) {
            case StoreEntity::TYPE_ORDER:
                $this->dto = new OrderData();

                break;

            case StoreEntity::TYPE_SHARE:
                $this->dto = new ShareData();

                break;

            default:
                $this->dto = new StoreEntityData();
        }
    }

    public function factory($data): DTO
    {
        return $this->dto::make($data);
    }
}
