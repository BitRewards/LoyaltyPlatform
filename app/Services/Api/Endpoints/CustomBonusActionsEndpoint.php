<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Endpoints\Traits\GlobalFiltersTrait;
use App\Services\Api\Specification\ApiOperation;

class CustomBonusActionsEndpoint extends ApiEndpoint
{
    use GlobalFiltersTrait;

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/actions/custom';
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
            'summary' => __('Available actions with the CustomBonus type'),
            'description' => __('Returns list of available Actions with CustomBonus type'),
            'parameters' => $this->globalFiltersParameters(),
            'tags' => [__('Actions')],
            'responses' => [
                $this->jsonArray(__('List of Actions'), 'Action'),
            ],
        ]);
    }
}
