<?php

namespace App\Http\Controllers\Api;

use App\DTO\StoreEventData;
use App\Http\Controllers\Controller;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class InsalesController extends Controller
{
    public function webhook(Request $request)
    {
        $processImmediately = Input::get('process_immediately');

        $data = $request->request->all();

        $eventData = new StoreEventData(StoreEvent::ACTION_STORE);
        $eventData->entityType = StoreEntity::TYPE_ORDER;
        $eventData->data = $data;
        $eventData->converterType = StoreEvent::CONVERTER_TYPE_INSALES_ORDER;

        app(StoreEventService::class)->saveEvent(
            \Auth::user()->partner,
            $eventData,
            $processImmediately
        );

        return 'ok';
    }
}
