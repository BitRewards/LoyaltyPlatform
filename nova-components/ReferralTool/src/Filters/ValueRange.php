<?php

namespace Bitrewards\ReferralTool\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ValueRange extends Filter
{
    public $component = 'value-range';

    protected $column;

    public function __construct(string $column, ?string $name = null)
    {
        $this->column = $column;
        $this->setName($name);
        $this->withMeta([
            'translates' => [
                'from_label' => __('Select from'),
                'to_label' => __('Select to'),
            ],
        ]);
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function apply(Request $request, $query, $value)
    {
        if (!empty($value['from'])) {
            $query->where($this->column, '>=', $value['from']);
        }

        if (!empty($value['to'])) {
            $query->where($this->column, '<=', $value['to']);
        }
    }

    public function options(Request $request): array
    {
        return [];
    }
}
