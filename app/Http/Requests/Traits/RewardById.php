<?php

namespace App\Http\Requests\Traits;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

trait RewardById
{
    private $reward;

    public function validateRewardId(MessageBag $messageBag)
    {
        $rewardId = $this->route('rewardId') ?? $this->id;

        $this->reward = Reward::where('id', $rewardId)->first();

        if (!$this->reward) {
            $messageBag->add('id', 'Reward not found');

            return;
        }

        if ($this->reward->partner_id != \Auth::user()->partner_id && !\Auth::user()->can('admin')) {
            $messageBag->add('user_key', 'Permission Denied');

            return;
        }
    }

    /**
     * @return Reward|null
     */
    public function getReward()
    {
        return $this->reward;
    }
}
