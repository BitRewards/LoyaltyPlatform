<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Actions\ShowCustomBonusActionsRequest;
use App\Transformers\ActionTransformer;

class CustomBonusActionsController extends ApiController
{
    /**
     * @var ActionTransformer
     */
    private $actionTransformer;

    public function __construct(ActionTransformer $actionTransformer)
    {
        $this->actionTransformer = $actionTransformer;
    }

    /**
     * @param ShowCustomBonusActionsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ShowCustomBonusActionsRequest $request)
    {
        $bonusActions = $request->user()->partner->customBonusActions();

        return response()->json(
            fractal($bonusActions, $this->actionTransformer)
        );
    }
}
