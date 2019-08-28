<?php

namespace App\Listeners\Auth;

use App\Administrator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Authenticated;

class LogAuthenticated
{
    public function handle(Authenticated $event)
    {
        $user = $event->user;

        if ($user instanceof Administrator) {
            $user->last_visited_at = Carbon::now();
            $user->save();
        } elseif (($user instanceof User || $user instanceof \App\User) && $user->person) {
            $user->person->last_visited_at = Carbon::now();
            $user->person->save();
        }
    }
}
