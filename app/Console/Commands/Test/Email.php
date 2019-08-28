<?php

namespace App\Console\Commands\Test;

use App\Mail\BalanceChanged;
use App\Models\User;
use Illuminate\Console\Command;

class Email extends Command
{
    protected $signature = 'test:email';

    public function handle()
    {
        \Mail::send(new BalanceChanged(User::whereEmail('**REMOVED**')->first(), 100));
        /*\Mail::raw('test', function($message) {
            $message->subject('subject');
            $message->to('artemzr@gmail.com');
        });*/
    }
}
