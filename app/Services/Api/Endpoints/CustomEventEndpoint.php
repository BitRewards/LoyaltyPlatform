<?php

namespace App\Services\Api\Endpoints;

use App\Models\StoreEvent;
use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;
use App\Services\Api\Specification\Responses\TextResponse;

class CustomEventEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/events/custom';
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
            'summary' => __('Custom Event'),
            'description' => __('Saves and processes custom event'),
            'parameters' => [
                $this->stringInput('entity_type', __('Entity Type'))->required(),
                $this->integerInput('entity_external_id', __("Entity's external ID")),
                $this->stringInput('data', __('Event data (JSON)')),
                $this->stringInput('action', __('Action Type'), StoreEvent::actions()),
                $this->stringInput('converter_type', __('Converter type')),
                $this->booleanInput('process_immediately', __('Process this event immediately')),
            ],
            'tags' => [__('Events')],
            'responses' => [
                new TextResponse(
                    __('The \'Ok\' response in case the request is successfully processed or the event is queued')
                ),
            ],
        ]);
    }
}
