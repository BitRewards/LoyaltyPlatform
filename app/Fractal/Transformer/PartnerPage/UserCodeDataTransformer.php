<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\UserCodeData;
use League\Fractal\TransformerAbstract;

class UserCodeDataTransformer extends TransformerAbstract
{
    public function transform(UserCodeData $userCodeData): array
    {
        return [
            'loyaltyCard' => \strip_tags($userCodeData->loyaltyCard),
            'acquiredAt' => $userCodeData->acquiredAt ?? null,
        ];
    }
}
