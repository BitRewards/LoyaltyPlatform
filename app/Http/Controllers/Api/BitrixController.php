<?php

namespace App\Http\Controllers\Api;

use App\DTO\StoreEventData;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\NormalizedPostData;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Http\Request;

class BitrixController extends Controller
{
    use NormalizedPostData;

    public function storeOrder(Request $request)
    {
        $partner = \Auth::user()->partner;

        if (!$partner) {
            return 'partner not found';
        }

        $orderData = $this->getNormalizedPostData();

        $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
        $eventData->entityType = StoreEntity::TYPE_ORDER;
        $eventData->data = $orderData;
        $eventData->converterType = StoreEvent::CONVERTER_TYPE_BITRIX_ORDER;

        app(StoreEventService::class)->saveEvent(
            $partner,
            $eventData,
            false
        );

        return 'ok';
    }
}
