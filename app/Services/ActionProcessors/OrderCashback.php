<?php

namespace App\Services\ActionProcessors;

use App\Models\StoreEntity;

class OrderCashback extends Base
{
    protected $requiresEntity = true;
    protected $requiresEntityConfirmation = true;

    public function getEntityType()
    {
        return StoreEntity::TYPE_ORDER;
    }
}
