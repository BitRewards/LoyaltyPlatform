<?php

namespace App\Console\Commands\Test;

use GL\Rabbit\DTO\RPC\GIFTD\TestRequest;
use GL\Rabbit\RpcClient;
use Illuminate\Console\Command;

class Rabbit extends Command
{
    public $signature = 'test:rabbit';

    public function handle(RpcClient $client)
    {
        dump($client->call(new TestRequest('Hello world!')));
    }
}
