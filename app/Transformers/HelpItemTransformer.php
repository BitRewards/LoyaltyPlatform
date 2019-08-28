<?php

namespace App\Transformers;

use App\Models\HelpItem;
use League\Fractal\TransformerAbstract;

class HelpItemTransformer extends TransformerAbstract
{
    public function transform(HelpItem $item)
    {
        return [
            'question' => $item->question,
            'answer' => $item->answer,
        ];
    }
}
