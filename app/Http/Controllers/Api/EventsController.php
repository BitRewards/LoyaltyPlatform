<?php

namespace App\Http\Controllers\Api;

use App\DTO\StoreEventData;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Traits\NormalizedPostData;
use App\Http\Requests\Api\CustomEventRequest;
use App\Http\Requests\Api\OrderEventRequest;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\StoreEventService;

class EventsController extends ApiController
{
    use NormalizedPostData;

    /**
     * Handle order event request.
     *
     * @param OrderEventRequest $request
     * @param StoreEventService $storeEventService
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function order(OrderEventRequest $request, StoreEventService $storeEventService)
    {
        $processImmediately =
            $request->has('process_immediately') &&
            1 === intval($request->input('process_immediately'));

        $orderData = $this->getNormalizedPostData();

        $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
        $eventData->entityType = StoreEntity::TYPE_ORDER;
        $eventData->data = $orderData;
        $eventData->converterType = StoreEvent::CONVERTER_TYPE_API_ORDER;

        app(StoreEventService::class)->saveEvent(
            \Auth::user()->partner,
            $eventData,
            $processImmediately
        );

        return jsonResponse(['status' => 'ok']);
    }

    /**
     * Handle custom event request.
     *
     * @param CustomEventRequest $request
     * @param StoreEventService  $storeEventService
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function custom(CustomEventRequest $request, StoreEventService $storeEventService)
    {
        $processImmediately = $request->has('process_immediately') && 1 === intval($request->input('process_immediately'));

        $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
        $eventData->entityType = $request->input('entity_type');
        $eventData->data = $request->input('data', []);

        if ($request->has('entity_external_id')) {
            $eventData->entityExternalId = $request->input('entity_external_id');
        }

        if ($request->has('converter_type')) {
            $eventData->converterType = $request->input('converter_type');
        }

        $storeEventService->saveEvent($request->user()->partner, $eventData, $processImmediately);

        return jsonResponse(['status' => 'ok']);
    }
}
