<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UsersSearchEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/search/users';
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
            'summary' => __('Users search'),
            'description' => __('Performs users search by name, phone, email or loyalty card number'),
            'parameters' => [
                $this->stringQuery('query', __('Search query')),
            ],
            'tags' => [__('Users'), __('Search')],
            'responses' => [
                $this->jsonArray(__('Users list'), 'User'),
            ],
        ]);
    }
}
