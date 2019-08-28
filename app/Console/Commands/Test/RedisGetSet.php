<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;

class RedisGetSet extends Command
{
    protected $signature = 'test:redis';

    public function handle()
    {
        \Redis::set('test-key', 'test-value');
        var_dump(\Redis::get('test-key'));
    }
}
