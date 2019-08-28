<?php

namespace App\Services;

use App\Models\SpecialOfferAction;
use App\Models\SpecialOfferReward;
use Illuminate\Support\Collection;

class SpecialOfferService
{
    /**
     * @var SpecialOfferAction
     */
    protected $specialOfferActionModel;

    /**
     * @var SpecialOfferReward
     */
    protected $specialOfferRewardModel;

    public function __construct(
        SpecialOfferAction $specialOfferActionModel,
        SpecialOfferReward $specialOfferRewardModel
    ) {
        $this->specialOfferActionModel = $specialOfferActionModel;
        $this->specialOfferRewardModel = $specialOfferRewardModel;
    }

    public function getActionsCount(): int
    {
        return $this->specialOfferActionModel->count();
    }

    public function getActionList(int $page, int $perPage = 20): Collection
    {
        return $this
            ->specialOfferActionModel::with('action', 'action.partner')
            ->orderBy('weight', 'desc')
            ->forPage($page, $perPage)
            ->get();
    }

    public function getRewardsCount(): int
    {
        return $this->specialOfferRewardModel->count();
    }

    public function getRewardList(int $page, int $perPage = 20): Collection
    {
        return $this
            ->specialOfferRewardModel::with('reward')
            ->orderBy('weight', 'desc')
            ->forPage($page, $perPage)
            ->get();
    }
}
