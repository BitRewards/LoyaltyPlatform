<?php

namespace Bitrewards\ReferralTool\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\DateFilter;

class ToDateFilter extends DateFilter
{
    /**
     * @var string
     */
    protected $column;

    public function __construct(string $column, ?string $name = null)
    {
        $this->column = $column;
        $this->setName($name);
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @param array   $value
     */
    public function apply(Request $request, $query, $value): void
    {
        if ($value) {
            $query->where($this->column, '<=', Carbon::make($value)->endOfDay());
        }
    }

    public function key()
    {
        return get_class($this).'.'.$this->column;
    }
}
