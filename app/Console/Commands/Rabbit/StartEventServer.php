<?php

namespace App\Console\Commands\Rabbit;

use App\Console\Commands\Rabbit\Traits\RabbitStarter;
use App\Rabbit\Handler\CouponUpdateHandler;
use App\Rabbit\Handler\PartnerUpdateHandler;
use GL\Rabbit\DTO\Events\CouponUpdate;
use GL\Rabbit\DTO\Events\PartnerUpdate;
use Illuminate\Console\Command;

class StartEventServer extends Command
{
    use RabbitStarter;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:startEventServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts Rabbit Event Server for all events';

    public function handle(\GL\Rabbit\EventServer $eventServer)
    {
        $this->sleepAndExitIfRabbitIsUnavailable();

        $eventServer->addHandler(PartnerUpdate::class, app(PartnerUpdateHandler::class));
        $eventServer->addHandler(CouponUpdate::class, app(CouponUpdateHandler::class));

        $eventServer->run('*');
    }
}
