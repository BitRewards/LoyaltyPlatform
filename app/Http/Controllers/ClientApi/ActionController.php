<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\StoreEventData;
use App\Http\Controllers\ClientApiController;
use App\Http\Requests\PerformAction;
use App\Models\Action;
use App\Models\Partner;
use App\Models\User;
use App\Services\ActionService;
use App\Services\UserService;
use App\Transformers\ActionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ActionController extends ClientApiController
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ActionService
     */
    private $actionService;

    /**
     * @var ActionTransformer
     */
    private $actionTransformer;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        Auth $auth,
        ActionTransformer $actionTransformer,
        ActionService $actionService,
        UserService $userService
    ) {
        $this->auth = $auth;
        $this->actionTransformer = $actionTransformer;
        $this->actionService = $actionService;
        $this->userService = $userService;
    }

    public function actionList(Partner $partner): JsonResponse
    {
        $actions = $this
            ->actionService
            ->getPartnerActions($partner, $this->auth::user(), [Input::get('tag', ''), '*']);

        return $this->responseJsonCollection(fractal($actions, $this->actionTransformer));
    }

    public function get(Action $action): JsonResponse
    {
        if (Action::STATUS_ENABLED != $action->status) {
            abort(404);
        }

        $action->load('specialOfferAction');

        return $this->responseJson(fractal($action, $this->actionTransformer));
    }

    public function perform(PerformAction $request): JsonResponse
    {
        $action = $request->action;

        $storeEventType = $action->getCorrespondingStoreEventType();

        /**
         * @var User
         */
        $user = $this->auth::user();

        if (!$storeEventType) {
            return $this->responseJson(
                $this->serializeBalance($user)
            );
        }

        $data = \Request::all();

        $data[StoreEventData::DATA_KEY_TARGET_ACTION_ID] = $action->id;

        $this->userService->processSystemEvent(
            $user,
            $storeEventType,
            $data
        );

        $user->refresh();

        return $this->responseJson(
            $this->serializeBalance($user)
        );
    }

    private function serializeBalance(User $user)
    {
        return ['balance' => (float) $user->balance];
    }
}
