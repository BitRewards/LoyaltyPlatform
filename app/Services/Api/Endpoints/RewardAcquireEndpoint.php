<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class RewardAcquireEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/rewards/{rewardId}/acquire';
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
            'summary' => __('Acquire Reward'),
            'description' => __('Acquires selected Reward for given user'),
            'parameters' => [
                $this->integerPath('rewardId', __('Reward ID'))->required(),
                $this->stringInput('user_key', __('User Key'))->required(),
            ],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->jsonItem(__('Transaction'), 'Transaction'),
            ],
        ]);
    }
}
