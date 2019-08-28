<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Endpoints\Traits\GlobalFiltersTrait;
use App\Services\Api\Specification\ApiOperation;

class UserTransactionsEndpoint extends ApiEndpoint
{
    use GlobalFiltersTrait;

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/transactions';
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
            'summary' => __('Show user transactions'),
            'description' => __('Returns transactions list of given user'),
            'parameters' => $this->paginationParameters([
                $this->stringPath('userKey', __('User Key'))->required(),
            ]),
            'tags' => [__('Users'), __('Transactions')],
            'responses' => [
                $this->jsonArray(__('Transactions list'), 'Transaction'),
                $this->jsonError(__('User was not found'), 404),
            ],
        ]);
    }
}
