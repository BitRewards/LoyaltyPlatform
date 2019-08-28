<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    /**
     * Get input fields without NULL values.
     *
     * @param Request $request
     * @param array   $fields
     *
     * @return array
     */
    protected function inputsWithoutNulls(Request $request, array $fields)
    {
        $inputs = collect($request->only($fields));

        return $inputs->reject(null)->toArray();
    }

    /**
     * Apply all global filters (if required).
     *
     * @param Request $request
     * @param Builder $query
     *
     * @return Builder
     */
    protected function applyGlobalFilters(Request $request, Builder $query)
    {
        $this->applyCreatedAtFilter($request, $query);

        return $query;
    }

    /**
     * Apply "created_at" filter.
     *
     * @param Request $request
     * @param Builder $query
     *
     * @return $this
     */
    private function applyCreatedAtFilter(Request $request, Builder $query)
    {
        if (!$request->has('period')) {
            return $this;
        }

        $from = null;
        $till = null;

        switch ($request->input('period')) {
            case 'today':
                $from = strtotime('today 00:00:01');
                $till = strtotime('today 23:59:59');

                break;

            case 'this_week':
                $from = strtotime('first day of this week');
                $till = strtotime('last day of this week');

                break;

            case 'this_month':
                $from = strtotime('first day of this month');
                $till = strtotime('last day of this month');

                break;

            default:
                return $this;
        }

        $dateFrom = Carbon::createFromTimestamp(\HDate::adjust($from))->hour(0)->second(0);
        $dateTill = Carbon::createFromTimestamp(\HDate::adjust($till))->hour(23)->second(59);

        $query->whereBetween('created_at', [$dateFrom, $dateTill]);

        return $this;
    }
}
