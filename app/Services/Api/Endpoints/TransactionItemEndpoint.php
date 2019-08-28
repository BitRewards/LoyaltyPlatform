<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class TransactionItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/transactions/{transactionId}';
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
            'summary' => __('Show Transaction'),
            'description' => __('Returns Transaction by ID'),
            'parameters' => [
                $this->integerPath('transactionId', __('Transaction ID'))->required(),
            ],
            'tags' => [__('Transactions')],
            'responses' => [
                $this->jsonItem(__('Transaction'), 'Transaction'),
            ],
        ]);
    }
}
