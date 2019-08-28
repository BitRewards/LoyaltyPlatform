<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}';
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
            'summary' => __('Show User'),
            'description' => __('Returns User by key'),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
            ],
            'tags' => [__('Users')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
                $this->jsonError(__('User was not found'), 404),
            ],
        ]);
    }
}
