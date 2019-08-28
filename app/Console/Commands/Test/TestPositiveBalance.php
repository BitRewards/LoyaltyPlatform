<?php

namespace App\Console\Commands\Test;

use App\Mail\PositiveBalance;
use App\Models\User;
use Illuminate\Console\Command;

class TestPositiveBalance extends Command
{
    protected $signature = 'test:positive-balance {email}';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Sending test message to user with e-mail: {$email}");

        $user = User::whereEmail($email)->first();
        $result = \Mail::send(new PositiveBalance($user));

        var_dump($result);
    }
}
