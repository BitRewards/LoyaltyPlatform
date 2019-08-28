<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserBonusEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/bonus';
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
            'summary' => __('Give Bonus'),
            'description' => __('Gives bonus points to given User'),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
                $this->integerInput('bonus', __('Bonus points'))->required(),
                $this->stringInput('comment', __('Reason for issuing a bonus'))->required(),
                $this->integerInput('action_id', __('ID of the Action for which the bonus is issued')),
            ],
            'tags' => [__('Users'), __('Bonuses')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
                $this->jsonError(__('User was not found'), 404),
            ],
        ]);
    }
}
