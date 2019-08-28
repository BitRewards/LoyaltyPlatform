<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\ActionData;
use League\Fractal\TransformerAbstract;

class ActionDataTransformer extends TransformerAbstract
{
    /**
     * @var ActionViewDataTransform
     */
    protected $actionViewDataTransform;

    public function __construct(ActionViewDataTransform $actionViewDataTransform)
    {
        $this->actionViewDataTransform = $actionViewDataTransform;
    }

    public function transform(ActionData $actionData): array
    {
        return [
            'id' => $actionData->id,
            'type' => $actionData->type,
            'valueType' => $actionData->valueType,
            'title' => $actionData->viewData->title,
            'description' => $actionData->viewData->description,
            'viewData' => $this->actionViewDataTransform->transform($actionData->viewData),
        ];
    }
}
