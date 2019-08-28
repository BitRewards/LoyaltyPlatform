<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\ActionViewData;
use League\Fractal\TransformerAbstract;

class ActionViewDataTransform extends TransformerAbstract
{
    /**
     * @var \HAmount
     */
    private $amountHelper;

    public function __construct(\HAmount $amountHelper)
    {
        $this->amountHelper = $amountHelper;
    }

    public function transform(ActionViewData $actionViewData): array
    {
        return [
            'title' => $this->normalizeText($actionViewData->title),
            'description' => $this->normalizeText($actionViewData->description),
            'canBeDone' => $actionViewData->canBeDone,
            'impossibleReason' => $this->normalizeText($actionViewData->impossibleReason),
            'rewardAmount' => $this->normalizeText($actionViewData->rewardAmount),
            'iconClass' => $actionViewData->iconClass,
        ];
    }

    protected function normalizeText(string $text = null)
    {
        if ($text) {
            $text = str_replace($this->amountHelper::ROUBLE_BOLD, '₽', $text);
        }

        return $text;
    }
}
