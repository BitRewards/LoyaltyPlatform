<?php

namespace App\Http\Controllers\Api;

use App\DTO\StoreEventData;
use App\Http\Controllers\Controller;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\EventbriteService;
use App\Services\StoreEventService;
use Illuminate\Http\Request;

class EventbriteController extends Controller
{
    public function webhook(Request $request)
    {
        $partner = \Auth::user()->partner;

        if (!$partner) {
            return 'partner not found';
        }

        $data = $request->json();
        /**
         * @var \Symfony\Component\HttpFoundation\ParameterBag
         */
        $data = $data->all();

        // sample $data: {"config": {"action": "order.updated", "user_id": "203440600912", "endpoint_url": "http://requestb.in/1brz9ni1", "webhook_id": "331094"}, "api_url": "https://www.eventbriteapi.com/v3/orders/600006183/"}
        // \Log::debug(\HJson::encode($data));
        $action = $data['config']['action'] ?? null;

        if (0 !== strpos($action, 'order.')) {
            return 'not processing this event';
        }

        $apiUrl = $data['api_url'] ?? null;

        if (!$apiUrl) {
            return 'no api_url';
        }

        $parts = preg_split('#/#', $apiUrl, -1, PREG_SPLIT_NO_EMPTY);
        $orderId = end($parts);

        if (!is_numeric($orderId)) {
            return 'not numeric order_id';
        }

        $orderData = app(EventbriteService::class)->getOrderData($partner, $orderId);

        $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
        $eventData->entityType = StoreEntity::TYPE_ORDER;
        $eventData->data = $orderData;
        $eventData->converterType = StoreEvent::CONVERTER_TYPE_EVENTBRITE_ORDER;

        app(StoreEventService::class)->saveEvent(
            \Auth::user()->partner,
            $eventData,
            false
        );

        return 'ok';
    }
}
