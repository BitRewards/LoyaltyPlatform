<?php

namespace App\Services\Api\Endpoints\Traits;

trait GlobalFiltersTrait
{
    /**
     * Applies global parameters (pagination & periods).
     *
     * @param bool  $withPagination = true
     * @param array $mergeWith      = []
     *
     * @return array
     */
    public function globalFiltersParameters(bool $withPagination = true, array $mergeWith = [])
    {
        $global = [
            $this->stringQuery('period', __('Creation date period'), ['today', 'this_week', 'this_month']),
        ];

        if (true === $withPagination) {
            $global = array_merge($global, $this->paginationParameters());
        }

        return array_merge($global, $mergeWith);
    }

    /**
     * Applies pagination parameter.
     *
     * @param array $mergeWith = []
     *
     * @return array
     */
    public function paginationParameters(array $mergeWith = [])
    {
        return array_merge([
            $this->integerQuery('page', __('Page number (paginated results)')),
        ], $mergeWith);
    }
}
