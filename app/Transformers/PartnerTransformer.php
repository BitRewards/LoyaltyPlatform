<?php

namespace App\Transformers;

use App\Models\Partner;
use League\Fractal\TransformerAbstract;

class PartnerTransformer extends TransformerAbstract
{
    /**
     * Transform Partner model.
     *
     * @param \App\Models\Partner $partner
     *
     * @return array
     */
    public function transform(Partner $partner)
    {
        return [
            'title' => $partner->title,
            'url' => $partner->url,
            'id' => $partner->id,
        ];
    }
}
