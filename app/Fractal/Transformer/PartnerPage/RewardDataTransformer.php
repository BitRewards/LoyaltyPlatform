<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\RewardData;
use League\Fractal\TransformerAbstract;

class RewardDataTransformer extends TransformerAbstract
{
    /**
     * @var \HAmount
     */
    private $amountHelper;

    public function __construct(\HAmount $amountHelper)
    {
        $this->amountHelper = $amountHelper;
    }

    public function transform(RewardData $rewardData): array
    {
        return [
            'id' => $rewardData->id,
            'type' => $rewardData->type,
            'valueType' => $rewardData->valueType,
            'title' => $this->normalizeText($rewardData->title),
            'description' => $this->normalizeText($rewardData->description),
            'shortDescription' => $this->normalizeText($rewardData->shortDescription),
            'price' => $rewardData->price,
        ];
    }

    protected function normalizeText($text): string
    {
        $text = str_replace($this->amountHelper::ROUBLE_REGULAR, '₽', $text);

        return \strip_tags($text);
    }
}
