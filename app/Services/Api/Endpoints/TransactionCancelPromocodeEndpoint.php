<?php

namespace App\Services\Api\Endpoints;

use App\Services\Api\ApiEndpoint;
use App\Services\Api\Specification\ApiOperation;

class TransactionCancelPromocodeEndpoint extends ApiEndpoint
{
    /**
     * Get the endpoint path.
     *
     * @return string
     */
    public function path()
    {
        return '/transactions/{transactionId}/cancelPromoCode';
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
            'summary' => __('Cancel promo code'),
            'description' => __('Cancels promo code'),
            'parameters' => [
                $this->integerPath('transactionId', __('Transaction ID'))->required(),
            ],
            'tags' => [__('Transactions'), __('Loyalty Cards')],
            'responses' => [
                $this->emptyResponse(__('Empty Response')),
                $this->jsonError(__('Linked Reward was not found'), 404),
                $this->jsonError(__('Linked Reward is not a Giftd Reward')),
            ],
        ]);
    }
}
