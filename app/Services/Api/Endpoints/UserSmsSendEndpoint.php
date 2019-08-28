<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserSmsSendEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/sms/send';
    }

    /**
     * HTTP POST method operation.
     *
     * @return ApiOperation
     */
    public function post()
    {
        return new ApiOperation([
            'method' => 'POST',
            'summary' => __('Send phone verification SMS'),
            'description' => __("Initializes User's phone verification procedure"),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
            ],
            'tags' => [__('Users')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
            ],
        ]);
    }
}
