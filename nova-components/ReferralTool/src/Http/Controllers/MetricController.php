<?php

namespace Bitrewards\ReferralTool\Http\Controllers;

use Bitrewards\ReferralTool\ToolServiceProvider;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\MetricRequest;
use Laravel\Nova\Metrics\Metric;

class MetricController extends Controller
{
    public function show(MetricRequest $request)
    {
        /** @var Metric|null $metric */
        $metric = collect(ToolServiceProvider::cards())
            ->filter
            ->authorize($request)
            ->whereInstanceOf(Metric::class)
            ->first(function (Metric $metric) use ($request) {
                return $metric->uriKey() === $request->metric;
            });

        if (!$metric) {
            abort(404);
        }

        return response()->json([
            'value' => $metric->resolve($request),
        ]);
    }
}
