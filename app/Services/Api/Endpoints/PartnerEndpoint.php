<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class PartnerEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/partner';
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
            'summary' => __("Authenticated user's partner"),
            'description' => __('Returns authenticated partner\'s info'),
            'parameters' => [],
            'tags' => [__('Partners'), __('Users')],
            'responses' => [
                $this->jsonItem(__('Partner info'), 'Partner'),
            ],
        ]);
    }

    public function post()
    {
        return new ApiOperation([
            'method' => 'POST',
            'summary' => __('Creates a new partner'),
            'description' => __('Creates a new partner info'),
            'parameters' => [
                $this->stringInput('title', _("The partner's title"))->required(),
                $this->stringInput('email', _('Unique contact email provided by partner'))->required(),
                $this->stringInput('template', _('The partner template')),
                $this->stringInput('options', _('The partner custom options')),
            ],
            'tags' => [__('Partners')],
            'responses' => [
                $this->jsonItem(__('Created partner info'), 'CreatedPartner'),
            ],
        ]);
    }
}
