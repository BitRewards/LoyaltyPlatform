<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UsersEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/users';
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
            'summary' => __('New User'),
            'description' => __('Creates new User'),
            'parameters' => [
                $this->stringInput('name', __('Name'))->required(),
                $this->stringInput('phone', __('Customer Phone'))->required(),
                $this->stringInput('email', __('Email'))->required(),
                $this->stringInput('token', __('Loyalty Card token')),
                $this->stringInput('promo_code', __('Promo code (for referral program needs)')),
                $this->stringPath('return_existing_user', __('Return existing user if already exists (0/1)')),
                $this->stringPath('force_email_confirmation', __('Force email confirmation (0/1)')),
            ],
            'tags' => [__('Users')],
            'responses' => [
                $this->jsonItem(__('User'), 'User'),
            ],
        ]);
    }
}
