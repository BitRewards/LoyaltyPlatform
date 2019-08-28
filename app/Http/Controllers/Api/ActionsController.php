<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Actions\DestroyActionRequest;
use App\Http\Requests\Api\Actions\ShowActionRequest;
use App\Http\Requests\Api\Actions\StoreActionRequest;
use App\Http\Requests\Api\Actions\UpdateActionRequest;
use App\Models\Action;
use App\Transformers\ActionTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ActionsController extends ApiController
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
     * List available Actions.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Action::where('partner_id', $request->user()->partner->id);
        $paginator = $this->applyGlobalFilters($request, $query)->paginate();

        return response()->json(
            fractal($paginator, $this->actionTransformer)
        );
    }

    /**
     * Show single Action.
     *
     * @param ShowActionRequest $request
     * @param Action            $action
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowActionRequest $request, Action $action)
    {
        return response()->json(
            fractal($action, $this->actionTransformer)
        );
    }

    /**
     * Create new Action.
     *
     * @param StoreActionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreActionRequest $request)
    {
        $action = Action::create(
            array_merge(
                $this->inputsWithoutNulls($request, [
                    'type', 'value', 'value_type', 'title',
                    'description', 'config', 'status', 'tag',
                ]),
                ['partner_id' => $request->user()->partner->id]
            )
        );

        return response()->json(
            fractal($action, $this->actionTransformer)
        );
    }

    /**
     * Update the action.
     *
     * @param UpdateActionRequest $request
     * @param Action              $action
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateActionRequest $request, Action $action)
    {
        $patch = $this->inputsWithoutNulls($request, [
            'type', 'value', 'value_type', 'title',
            'description', 'config', 'status', 'tag',
        ]);

        $action->update($patch);

        return response()->json(
            fractal($action->fresh(), $this->actionTransformer)
        );
    }

    /**
     * Destroy the Action.
     *
     * @param DestroyActionRequest $request
     * @param Action               $action
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(DestroyActionRequest $request, Action $action)
    {
        $action->delete();

        return response('', 204);
    }
}
