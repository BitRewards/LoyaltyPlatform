<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class CodeItemEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/codes/{codeId}';
    }

    /**
     * Get the shared parameters.
     *
     * @return array
     */
    public function parameters()
    {
        return [
            $this->integerPath('codeId', __('Loyalty Card ID'))->required(),
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
            'summary' => __('Show the Loyalty Card'),
            'description' => __('Returns Loyalty Card by ID'),
            'parameters' => [],
            'tags' => [__('Loyalty Cards')],
            'responses' => [
                $this->jsonItem(__('Loyalty Card'), 'Code'),
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
            'summary' => __('Edit Loyalty Card'),
            'description' => __('Edits Loyalty Card'),
            'parameters' => [
                $this->stringInput('token', __('Unique Loyalty Card Token')),
                $this->integerInput('bonus_points', __('Bonus points')),
            ],
            'tags' => [__('Loyalty Cards')],
            'responses' => [
                $this->jsonItem(__('Updated Loyalty Card'), 'Code'),
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
            'summary' => __('Delete Loyalty Card'),
            'description' => __('Deletes Loyalty Card'),
            'parameters' => [],
            'tags' => [__('Loyalty Cards')],
            'responses' => [
                $this->emptyResponse(__('Empty Response')),
            ],
        ]);
    }
}
