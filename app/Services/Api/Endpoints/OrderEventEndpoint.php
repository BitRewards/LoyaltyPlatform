<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;
use App\Services\Api\Specification\Responses\JsonResponse;

class OrderEventEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/events/order';
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
            'summary' => __('Order Event'),
            'description' => __('Saves and processes information about the order. Use this method to give customers cashback and referral bonus for their purchases and purchases of their friends'),
            'parameters' => [
                $this->stringInput('id', __('Order id'))->required(),
                $this->stringInput('email', __("Customer's email")),
                $this->stringInput('phone', __("Customer's phone")),
                $this->stringInput('user_crm_key', __("Customer's User Key (put here the value of 'gcrm_user_key' cookie)")),
                $this->stringInput('ref_user_crm_key', __("Customer referrer's User Key (put here the value of 'gcrm_ref_user_key' cookie)")),
                // $this->stringInput('status_autofinishes_at', __('Status autofinish date in Y-m-d H:i:s format')),
                $this->stringInput('name', __("Customer's name")),
                $this->integerInput('amount_total', __('Order total amount')),
                $this->booleanInput('is_paid', __('Indicates that order is paid')),
                $this->booleanInput('is_delivered', __('Indicates that order is delivered')),
                $this->stringInput('comment', __("Customer's comment to the order")),
                $this->stringInput('manager_comment', __('Manager of the shop comment to the order')),
                $this->arrayInput('promo_codes', __('Promo Codes list'), 'string'),
                $this->floatInput('predefined_cashback', __('Predefined cashback value (put if you want to rewrite action value)')),
                $this->floatInput('predefined_referrer_cashback', __('Predefined referrer cashback value (put if you want to rewrite action value)')),
                // $this->booleanInput('process_immediately', __('Process this event immediately')),
            ],
            'tags' => [__('Events')],
            'responses' => [
                new JsonResponse(
                    \HJson::encode(['status' => 'ok'])
                ),
            ],
        ]);
    }
}
