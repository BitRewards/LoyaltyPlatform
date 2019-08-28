<?php

namespace App\DTO\Factory;

use App\DTO\SpecialOfferRewardData;
use App\Models\Reward;
use App\Models\SpecialOfferReward;
use Illuminate\Support\Collection;

class SpecialOfferRewardFactory
{
    /**
     * @var RewardFactory
     */
    protected $rewardFactory;

    public function __construct(RewardFactory $rewardFactory)
    {
        $this->rewardFactory = $rewardFactory;
    }

    public function factory(SpecialOfferReward $specialOfferReward): SpecialOfferRewardData
    {
        return SpecialOfferRewardData::make([
            'id' => $specialOfferReward->id,
            'brand' => $specialOfferReward->brand,
            'image' => $specialOfferReward->image_url,
            'reward' => $this->rewardFactory->factory($specialOfferReward->reward),
        ]);
    }

    /**
     * @param Collection $collection
     *
     * @return Collection|Reward[]
     */
    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (SpecialOfferReward $reward) {
            return $this->factory($reward);
        });
    }
}
