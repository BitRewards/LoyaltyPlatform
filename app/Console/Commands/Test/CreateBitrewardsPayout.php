<?php

namespace App\Console\Commands\Test;

use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class CreateBitrewardsPayout extends Command
{
    protected $signature = 'test:bitrewards';

    public function handle()
    {
        $reward = Reward::model()->whereAttributes([
            'type' => Reward::TYPE_BITREWARDS_PAYOUT,
        ])->first();

        $user = User::model()->whereAttributes([
            'name' => '**REMOVED**',
            'partner_id' => $reward->partner_id,
        ])->first();

        $transaction = $reward->getRewardProcessor()->acquire($user, [
            Transaction::DATA_POINTS_TO_SPEND => 100,
            Transaction::DATA_ETHEREUM_ADDRESS => '**REMOVED**',
        ], $user);

        var_dump($transaction->toArray());

        die;
    }
}
