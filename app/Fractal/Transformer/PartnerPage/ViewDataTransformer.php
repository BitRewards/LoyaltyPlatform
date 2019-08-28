<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\ViewData;
use League\Fractal\TransformerAbstract;

class ViewDataTransformer extends TransformerAbstract
{
    public function transform(ViewData $viewData): array
    {
        return [
            'cabinetTitle' => \strip_tags($viewData->cabinetTitle),
            'earnMessage' => $viewData->earnMessage,
            'spendMessage' => $viewData->spendMessage,
            'discountInsteadOfLoyaltyMessage' => $viewData->discountInsteadOfLoyaltyMessage,
            'rewardNAmountMessage' => $viewData->rewardNAmountMessage,
            'activatePlasticBeforeOtherActions' => $viewData->activatePlasticBeforeOtherActions,
        ];
    }
}
