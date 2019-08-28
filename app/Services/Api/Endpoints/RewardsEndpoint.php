<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class RewardsEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/rewards';
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
            'summary' => __('Available Rewards'),
            'description' => __('Returns list of available Rewards'),
            'parameters' => [
                $this->stringQuery('user_key', __('User Key')),
                $this->integerQuery('total', __('The current amount of the order for which the discount amount will be calculated')),
            ],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->jsonArray(__('Rewards list'), 'Reward'),
            ],
        ]);
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
            'summary' => __('New Reward'),
            'description' => __('Creates new Reward'),
            'parameters' => [
                $this->stringInput('title', __('Reward name'))->required(),
                $this->integerInput('price', __('Reward price'))->required(),
                $this->stringInput('type', __('Reward type'), \HReward::types())->required(),
                $this->integerInput('value', __('Value'))->required(),
                $this->stringInput('value_type', __('Value type'), \HReward::valueTypes())->required(),
                $this->stringInput('status', __('Status'), \HReward::statuses())->required(),
                $this->stringInput('tag', __('Tag')),
                $this->stringInput('description', __('Description')),
                $this->stringInput('description_short', __('Short description')),
                $this->stringInput('config', __('Reward configuration (JSON)')),
            ],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->jsonItem(__('Created Reward'), 'Reward'),
            ],
        ]);
    }
}
