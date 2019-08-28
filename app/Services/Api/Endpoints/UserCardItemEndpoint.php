<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserCardItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/cards/{token}';
    }

    /**
     * HTTP DELETE method operation.
     *
     * @return ApiOperation
     */
    public function delete()
    {
        return new ApiOperation([
            'method' => 'DELETE',
            'summary' => __('Detach Card from User'),
            'description' => __('Detaches Card from given User'),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
                $this->stringPath('token', __('Card token'))->required(),
            ],
            'tags' => [__('Users'), __('Loyalty Cards')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
                $this->jsonError(__('Card was not found'), 404),
            ],
        ]);
    }
}
