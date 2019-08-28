<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Endpoints\Traits\GlobalFiltersTrait;
use App\Services\Api\Specification\ApiOperation;

class CodesEndpoint extends ApiEndpoint
{
    use GlobalFiltersTrait;

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/codes';
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
            'summary' => __('Available Loyalty Cards'),
            'description' => __('Returns list of available Loyalty Cards'),
            'parameters' => $this->globalFiltersParameters(),
            'tags' => [__('Loyalty Cards')],
            'responses' => [
                $this->jsonArray(__('List of Loyalty Cards'), 'Code'),
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
            'summary' => __('New Loyalty Card'),
            'description' => __('Creates new Loyalty Card'),
            'parameters' => [
                $this->stringInput('token', __('Unique Loyalty Card Token'))->required(),
                $this->integerInput('bonus_points', __('Bonus points'))->required(),
            ],
            'tags' => [__('Loyalty Cards')],
            'responses' => [
                $this->jsonItem(__('Created Loyalty Card'), 'Code'),
            ],
        ]);
    }
}
