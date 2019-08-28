<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Endpoints\Traits\GlobalFiltersTrait;
use App\Services\Api\Specification\ApiOperation;

class TransactionsEndpoint extends ApiEndpoint
{
    use GlobalFiltersTrait;

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/transactions';
    }

    /**
     * HTTP GET method operation.
     *
     * @return ApiOperation
     */
    public function get()
    {
        return new ApiOperation([
            'method' => 'GET',
            'summary' => __('List of transactions'),
            'description' => __("Returns transactions list of authenticated partner's users"),
            'parameters' => $this->globalFiltersParameters(),
            'tags' => [__('Transactions')],
            'responses' => [
                $this->jsonArray(__('List of transactions'), 'Transaction'),
            ],
        ]);
    }
}
