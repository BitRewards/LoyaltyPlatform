<?php

namespace App\Transformers;

use App\Models\Code;
use League\Fractal\TransformerAbstract;

class CodeTransformer extends TransformerAbstract
{
    public function transform(Code $code)
    {
        return [
            'id' => intval($code->id),
            'token' => $code->token,
            'bonus_points' => intval($code->bonus_points),
            'partner_id' => intval($code->partner_id),
        ];
    }
}
