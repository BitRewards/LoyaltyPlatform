<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AggregateCounterTrait
{
    public function rowCounter(Builder $builder): int
    {
        return \DB::selectOne(
            "SELECT COUNT(*) FROM ({$builder->toSql()}) as aggregate_query",
            $builder->getBindings()
        )->count;
    }
}
