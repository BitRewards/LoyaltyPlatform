<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class ActionItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/actions/{actionId}';
    }

    /**
     * Get the shared parameters.
     *
     * @return array
     */
    public function parameters()
    {
        return [
            $this->integerPath('actionId', __('Action ID'))->required(),
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
            'summary' => __('Show the Action'),
            'description' => __('Returns Action by ID'),
            'parameters' => [],
            'tags' => [__('Actions')],
            'responses' => [
                $this->jsonItem(__('Action'), 'Action'),
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
            'summary' => __('Edit the Action'),
            'description' => __('Updates Action'),
            'parameters' => [
                $this->stringInput('title', __('Action Name')),
                $this->stringInput('type', __('Action Type'), \HAction::getAllTypes()),
                $this->integerInput('value', __('Value')),
                $this->stringInput('value_type', __('Value type'), \HAction::valueTypes()),
                $this->stringInput('status', __('Status'), ['enabled', 'disabled']),
                $this->stringInput('tag', __('Tag')),
                $this->stringInput('description', __('Description')),
                $this->stringInput('config', __('Configuration (JSON)')),
            ],
            'tags' => [__('Actions')],
            'responses' => [
                $this->jsonItem(__('Updated Action'), 'Action'),
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
            'summary' => __('Delete Action'),
            'description' => __('Deletes Action'),
            'parameters' => [],
            'tags' => [__('Actions')],
            'responses' => [
                $this->emptyResponse(__('Empty Response')),
            ],
        ]);
    }
}
