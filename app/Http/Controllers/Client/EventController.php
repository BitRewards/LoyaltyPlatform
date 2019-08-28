<?php

namespace App\Http\Controllers\Client;

use App\DTO\StoreEventData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcquireCode;
use App\Http\Requests\EventProcess;
use App\Models\User;
use App\Services\CodeService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function process(EventProcess $request)
    {
        $storeEventType = $request->action->getCorrespondingStoreEventType();

        if (!$storeEventType) {
            return jsonResponse('ok');
        }

        $data = $request->all();

        $data[StoreEventData::DATA_KEY_TARGET_ACTION_ID] = $request->action->id;

        app(UserService::class)->processSystemEvent(
            \Auth::user(),
            $storeEventType,
            $data
        );

        return jsonResponse('ok');
    }

    public function acquireCode(AcquireCode $request)
    {
        app(CodeService::class)->acquire($request->user(), $request->getCode());

        return jsonResponse('ok');
    }
}
