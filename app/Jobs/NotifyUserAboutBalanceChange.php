<?php

namespace App\Jobs;

use App\Mail\BalanceChanged;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserAboutBalanceChange implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $oldBalance;

    public function __construct($userId, $oldBalance)
    {
        $this->userId = $userId;
        $this->oldBalance = $oldBalance;
    }

    public function handle()
    {
        $user = User::whereId($this->userId)->first();

        if (!$user || !$user->email || !($user->balance > 0)) {
            return;
        }
        $newBalance = (float) $user->balance;

        if (abs($newBalance - $this->oldBalance) < 0.00001) {
            // excluding small glitches
            return;
        }

        if ($user->partner->getSetting(Partner::SETTINGS_IS_ALL_NOTIFICATIONS_DISABLED)) {
            return;
        }

        \Mail::send(new BalanceChanged($user, $this->oldBalance));
    }
}
