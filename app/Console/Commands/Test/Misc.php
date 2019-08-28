<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;

class Misc extends Command
{
    protected $signature = 'test:misc';

    public function handle()
    {
        echo "\n\n".\HUser::normalizePhone('00000000000000000000000000000000', 'ru')."\n\n";
    }
}
