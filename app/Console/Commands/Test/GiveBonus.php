<?php

namespace App\Console\Commands\Test;

use App\DTO\CustomBonusData;
use App\Models\User;
use App\Services\PartnerService;
use App\Services\UserService;
use Illuminate\Console\Command;

class GiveBonus extends Command
{
    protected $signature = 'test:give-bonus {email} {points}';

    public function handle()
    {
        if (!\App::isLocal()) {
            return;
        }

        $email = $this->argument('email');
        $points = $this->argument('points');

        $partner = app(PartnerService::class)->getTestPartner();
        $user = User::model()->whereAttributes([
            'email' => $email,
            'partner_id' => $partner->id,
        ])->first();

        if (!$user) {
            $this->error("User with email {$email} not found (partner {$partner->id}, {$partner->key})");

            return;
        }

        app(UserService::class)->giveCustomBonusToUser(new CustomBonusData(
            $user,
            $points
        ));

        $this->info("$points bonus points successfully given to $email (user name = {$user->name}, user id = {$user->id})");
    }
}
