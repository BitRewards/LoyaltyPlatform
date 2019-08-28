<?php

namespace App\Fractal\Transformer;

use App\DTO\RewardData;
use League\Fractal\TransformerAbstract;

class PartnerRewardTransformer extends TransformerAbstract
{
    public function transform(RewardData $rewardData)
    {
        $reward = $rewardData->toArray();

        unset($reward['partner']);

        return $reward;
    }
}
