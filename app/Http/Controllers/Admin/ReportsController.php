<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShowReportRequest;
use App\Services\ReportsService;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $from = Carbon::createFromTimestamp(strtotime('first day of last month'));
        $till = Carbon::createFromTimestamp(strtotime('last day of last month'));

        return view('reports.index', [
            'dateFrom' => $from->format('Y-m-d'),
            'dateTill' => $till->format('Y-m-d'),
        ]);
    }

    /**
     * @param ShowReportRequest $request
     * @param ReportsService    $reports
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowReportRequest $request, ReportsService $reports)
    {
        $from = Carbon::createFromFormat('Y-m-d', $request->input('date_from'));
        $till = Carbon::createFromFormat('Y-m-d', $request->input('date_till'));

        if ($from >= $till) {
            return jsonError([
                'date_from' => [__('Wrong beginning date')],
            ]);
        }

        $report = $reports->generate($request->user()->partner, $from, $till);

        return response()->json([
            'data' => $report,
        ]);
    }
}
