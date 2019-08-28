<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class RewardItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/rewards/{rewardId}';
    }

    /**
     * Get the shared parameters.
     *
     * @return array
     */
    public function parameters()
    {
        return [
            $this->integerPath('rewardId', __('Reward ID'))->required(),
        ];
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
            'summary' => __('Show the Reward'),
            'description' => __('Returns Reward by ID'),
            'parameters' => [
                $this->integerQuery('total', __('The current amount of the order for which the discount amount will be calculated')),
            ],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->jsonItem(__('Reward'), 'Reward'),
            ],
        ]);
    }

    /**
     * HTTP PUT method operation.
     *
     * @return ApiOperation
     */
    public function put()
    {
        return new ApiOperation([
            'method' => 'PUT',
            'summary' => __('Edit the Reward'),
            'description' => __('Updates Reward'),
            'parameters' => [
                $this->stringInput('title', __('Reward name')),
                $this->integerInput('price', __('Reward price')),
                $this->stringInput('type', __('Reward type'), \HReward::types()),
                $this->integerInput('value', __('Value')),
                $this->stringInput('value_type', __('Value type'), \HReward::valueTypes()),
                $this->stringInput('status', __('Status'), \HReward::statuses()),
                $this->stringInput('tag', __('Tag')),
                $this->stringInput('description', __('Description')),
                $this->stringInput('description_short', __('Short description')),
                $this->stringInput('config', __('Reward configuration (JSON)')),
            ],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->jsonItem(__('Updated Reward'), 'Reward'),
            ],
        ]);
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
            'summary' => __('Delete Reward'),
            'description' => __('Deletes Reward'),
            'parameters' => [],
            'tags' => [__('Rewards')],
            'responses' => [
                $this->emptyResponse(__('Empty Response')),
            ],
        ]);
    }
}
