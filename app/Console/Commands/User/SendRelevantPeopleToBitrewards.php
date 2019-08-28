<?php

namespace App\Console\Commands\User;

use App\Models\Partner;
use Illuminate\Console\Command;
use App\Services\UserService;
use App\Models\User;

class SendRelevantPeopleToBitrewards extends Command
{
    protected $signature = 'user:sendRelevantPeopleToBitrewards';

    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $partners = Partner::all();
        $partner = null;

        foreach ($partners as $partner) {
            if ($partner->isBitrewardsDemoPartner()) {
                break;
            }
        }
        $this->info("Found BitRewards partner, id = {$partner->id}, title = $partner->title");

        $users = User::where('partner_id', $partner->id)->get();
        $total = count($users);

        foreach ($users as $i => $user) {
            app(UserService::class)->notifyBitrewards($user);
            $this->info("$i / $total");
        }
    }
}
