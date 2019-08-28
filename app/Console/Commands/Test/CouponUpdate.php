<?php

namespace App\Console\Commands\Test;

use App\Rabbit\Handler\CouponUpdateHandler;
use Illuminate\Console\Command;

class CouponUpdate extends Command
{
    protected $signature = 'test:CouponUpdate';

    public function handle()
    {
        $handler = app(CouponUpdateHandler::class);
        $event = new \GL\Rabbit\DTO\Events\CouponUpdate();
        $event->giftdPartnerId = 1;
        $event->status = 'used';
        $event->code = '**REMOVED**';
        $handler->handle($event);
    }
}
