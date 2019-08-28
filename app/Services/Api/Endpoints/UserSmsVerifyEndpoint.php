<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserSmsVerifyEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users/{userKey}/sms/verify';
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
            'summary' => __('Check phone verification code'),
            'description' => __('Checks phone verification code'),
            'parameters' => [
                $this->stringPath('userKey', __('User Key'))->required(),
                $this->stringInput('token', __('Verification code'))->required(),
            ],
            'tags' => [__('Users')],
            'responses' => [
                $this->jsonItem(__('Verification result'), 'SmsVerificationResult'),
            ],
        ]);
    }
}
