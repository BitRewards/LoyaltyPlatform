<?php

namespace Traits;

use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property \UnitTester $tester
 */
trait NovaRequestTrait
{
    public function novaRequest(
        $uri,
        $method = 'GET',
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ): NovaRequest {
        $request = NovaRequest::create(...\func_get_args());

        $request->setUserResolver(function () {
            return \Auth::user();
        });

        return $request;
    }

    public function simpleNovaRequest($parameters = []): NovaRequest
    {
        return $this->novaRequest('/', 'GET', $parameters);
    }
}
