<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\HelpItemData;
use League\Fractal\TransformerAbstract;

class HelpItemDataTransformer extends TransformerAbstract
{
    public function transform(HelpItemData $helpItemData): array
    {
        return [
            'question' => $helpItemData->question,
            'answer' => $helpItemData->answer,
        ];
    }
}
