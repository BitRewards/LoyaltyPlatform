<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class UserBonusExtendedEndpoint extends ApiEndpoint
{
    public function path(): string
    {
        return '/users/bonus';
    }

    public function post(): ApiOperation
    {
        return new ApiOperation([
            'method' => 'POST',
            'summary' => __('Give Bonus'),
            'description' => __('Gives the bonus to user by any identifier (user_key, email or phone). If the user does not exist in the system and the auto_create flag is passed, the user is created automatically. Either bonus or bonus_fiat parameter is required.'),
            'parameters' => [
                $this->integerInput('bonus', __('Bonus amount (in points or BIT tokens)')),
                $this->integerInput('bonus_fiat', __('Bonus amount (in fiat currency of the partner, such as USD)')),
                $this->stringInput('comment', __('Reason for issuing a bonus')),
                $this->integerInput('action_id', __('ID of the Action for which the bonus is issued (if known)')),
                $this->stringPath('user_key', __('User Key (if known)')),
                $this->stringPath('email', __('User email')),
                $this->stringPath('phone', __('User phone')),
                $this->stringPath('name', __('The name which will be assigned to the user while auto creating')),
                $this->stringPath('auto_create', __('Create user if not found in the system')),
            ],
            'tags' => [__('Users'), __('Bonuses')],
            'responses' => [
                $this->jsonItem(__('User data'), 'User'),
                $this->jsonError(__('User was not found'), 404),
                $this->jsonError(__('User cannot be created'), 400),
            ],
        ]);
    }
}
