<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserCardsEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/cards';
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
            'summary' => __('Attach Card to User'),
            'description' => __('Attaches new Card to given User'),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
                $this->stringInput('token', __('Card token'))->required(),
            ],
            'tags' => [__('Users'), __('Loyalty Cards')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
            ],
        ]);
    }
}
