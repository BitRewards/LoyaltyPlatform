<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Codes\DestroyCodeRequest;
use App\Http\Requests\Api\Codes\ShowCodeRequest;
use App\Http\Requests\Api\Codes\StoreCodeRequest;
use App\Http\Requests\Api\Codes\UpdateCodeRequest;
use App\Models\Code;
use App\Transformers\CodeTransformer;
use Illuminate\Http\Request;

class CodesController extends ApiController
{
    /**
     * Show codes list.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Code::where('partner_id', $request->user()->partner->id);
        $paginator = $this->applyGlobalFilters($request, $query)->paginate();

        return response()->json(
            fractal($paginator, new CodeTransformer())
        );
    }

    /**
     * Store new code.
     *
     * @param StoreCodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCodeRequest $request)
    {
        $code = Code::create(
            array_merge(
                $this->inputsWithoutNulls($request, ['token', 'bonus_points']),
                ['partner_id' => $request->user()->partner->id]
            )
        );

        return response()->json(
            fractal($code, new CodeTransformer())
        );
    }

    /**
     * Show the code.
     *
     * @param ShowCodeRequest $request
     * @param Code            $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowCodeRequest $request, Code $code)
    {
        return response()->json(
            fractal($code, new CodeTransformer())
        );
    }

    /**
     * Update the code.
     *
     * @param UpdateCodeRequest $request
     * @param Code              $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCodeRequest $request, Code $code)
    {
        //TODO: validation error if code is already attached to the user (!is_null($code->user_id))

        if (!is_null($code->user_id)) {
            return response()->json([
                'type' => 'error',
                'error' => [__('This token has already been used')],
            ], 422);
        }

        $code->update(
            $this->inputsWithoutNulls($request, [
                'token', 'bonus_points',
            ])
        );

        return response()->json(
            fractal($code->fresh(), new CodeTransformer())
        );
    }

    /**
     * Destroy the code.
     *
     * @param DestroyCodeRequest $request
     * @param Code               $code
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(DestroyCodeRequest $request, Code $code)
    {
        $code->delete();

        return response('', 204);
    }
}
