<?php

namespace App\Console\Commands\Rabbit;

use App\Console\Commands\Rabbit\Traits\RabbitStarter;
use App\Rabbit\Handler\GetLoyaltyPartnerDataHandler;
use App\Rabbit\Handler\PartnerStatisticHandler;
use App\Rabbit\Handler\SaveCouponHandler;
use GL\Rabbit\DTO\RPC\CRM\GetLoyaltyPartnerDataRequest;
use GL\Rabbit\DTO\RPC\CRM\PartnerStatisticRequest;
use GL\Rabbit\DTO\RPC\CRM\SaveCouponRequest;
use GL\Rabbit\Enums\Topic;
use Illuminate\Console\Command;

class StartRpcServer extends Command
{
    use RabbitStarter;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:startRpcServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts Rabbit RPC Server for some message type subset. Example: "update.*" or "rpc.crm"';

    public function handle(\GL\Rabbit\RpcServer $rpcServer)
    {
        $this->sleepAndExitIfRabbitIsUnavailable();

        // $rpcServer->addHandler(TestRequest::class, new \App\Services\RabbitHandlers\RPC\TestHandler());
        $rpcServer->addHandler(SaveCouponRequest::class, app(SaveCouponHandler::class));
        $rpcServer->addHandler(GetLoyaltyPartnerDataRequest::class, app(GetLoyaltyPartnerDataHandler::class));
        $rpcServer->addHandler(PartnerStatisticRequest::class, app(PartnerStatisticHandler::class));

        $rpcServer->run(Topic::RPC_CRM);
    }
}
