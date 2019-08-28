<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Endpoints\Traits\GlobalFiltersTrait;
use App\Services\Api\Specification\ApiOperation;

class ActionsEndpoint extends ApiEndpoint
{
    use GlobalFiltersTrait;

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/actions';
    }

    /**
     * HTTP GET method operation.
     *
     * @return \App\Services\Api\Specification\ApiOperation
     */
    public function get()
    {
        return new ApiOperation([
            'method' => 'GET',
            'summary' => __('Available Actions'),
            'description' => __('Returns list of available Actions'),
            'parameters' => $this->globalFiltersParameters(),
            'tags' => [__('Actions')],
            'responses' => [
                $this->jsonArray(__('List of Actions'), 'Action'),
            ],
        ]);
    }

    /**
     * HTTP POST method operation.
     *
     * @return \App\Services\Api\Specification\ApiOperation
     */
    public function post()
    {
        return new ApiOperation([
            'method' => 'POST',
            'summary' => __('New Action'),
            'description' => __('Creates new Action'),
            'consumes' => $this->urlencodedForm(),
            'parameters' => [
                $this->stringInput('title', __('Action Name'))->required(),
                $this->stringInput('type', __('Action Type'), \HAction::getAllTypes())->required(),
                $this->integerInput('value', __('Value'))->required(),
                $this->stringInput('value_type', __('Value type'), \HAction::valueTypes())->required(),
                $this->stringInput('status', __('Status'), ['enabled', 'disabled'])->required(),
                $this->stringInput('tag', __('Tag')),
                $this->stringInput('description', __('Description')),
                $this->stringInput('config', __('Configuration (JSON)')),
            ],
            'tags' => [__('Actions')],
            'responses' => [
                $this->jsonItem(__('Created Action'), 'Action'),
            ],
        ]);
    }
}
