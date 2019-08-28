<?php

namespace App\Nova\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\TrendResult;

trait TrendQueryTrait
{
    /**
     * @param Builder|EloquentBuilder $query
     * @param string                  $dateColumn
     *
     * @return Builder|EloquentBuilder
     */
    public function groupByDay($query, string $dateColumn)
    {
        $query
            ->addSelect(\DB::raw("to_char($dateColumn, 'yyyy-mm-dd') as metric_aggregate_date"))
            ->groupBy('metric_aggregate_date');

        return $query;
    }

    public function countByDay(Request $request, $query, string $dateColumn, string $column = '*'): TrendResult
    {
        $result = $this
            ->groupByDay($query, $dateColumn)
            ->addSelect(\DB::raw("COUNT($column) as metric_aggregate_count"))
            ->get()
            ->all();

        $map = Arr::pluck($result, 'metric_aggregate_count', 'metric_aggregate_date');

        $datePeriod = new \DatePeriod(
            Carbon::now()->subDays($request->get('range', 7)),
            new \DateInterval('P1D'),
            Carbon::now()
        );

        $trendData = [];

        /** @var \DateTime $date */
        foreach ($datePeriod as $date) {
            $formattedString = $date->format('Y-m-d');
            $trendData[$formattedString] = $map[$formattedString] ?? 0;
        }

        return (new TrendResult())->trend($trendData);
    }

    public function sumByDay(Request $request, $query, string $dateColumn, string $column = 'amount'): TrendResult
    {
        $result = $this
            ->groupByDay($query, $dateColumn)
            ->addSelect(\DB::raw("COALESCE(SUM($column), 0) as metric_aggregate_sum"));
        $result = $result->get()
            ->all();

        $map = Arr::pluck($result, 'metric_aggregate_sum', 'metric_aggregate_date');

        $datePeriod = new \DatePeriod(
            Carbon::now()->subDays($request->get('range', 7)),
            new \DateInterval('P1D'),
            Carbon::now()
        );

        $trendData = [];

        /** @var \DateTime $date */
        foreach ($datePeriod as $date) {
            $formattedString = $date->format('Y-m-d');
            $trendData[$formattedString] = $map[$formattedString] ?? 0.;
        }

        return (new TrendResult())->trend($trendData);
    }

    public function resultWithSum(TrendResult $result): TrendResult
    {
        $result->value = array_sum($result->trend);

        return $result;
    }
}
