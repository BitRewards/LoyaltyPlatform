<?php

namespace App\Providers;

use App\Services\Api\ApiDocsGenerator;
use App\Services\Api\Definitions\CouponDefinition;
use App\Services\Api\Definitions\CodeDefinition;
use App\Services\Api\Definitions\PartnerDefinition;
use App\Services\Api\Definitions\RewardDefinition;
use App\Services\Api\Definitions\SmsVerificationResultDefinition;
use App\Services\Api\Definitions\TransactionDefinition;
use App\Services\Api\Definitions\UserDefinition;
use App\Services\Api\Endpoints\ActionItemEndpoint;
use App\Services\Api\Endpoints\ChargeCouponEndpoint;
use App\Services\Api\Endpoints\CodeItemEndpoint;
use App\Services\Api\Endpoints\CodesEndpoint;
use App\Services\Api\Endpoints\CheckCouponEndpoint;
use App\Services\Api\Endpoints\CustomBonusActionsEndpoint;
use App\Services\Api\Endpoints\CustomEventEndpoint;
use App\Services\Api\Endpoints\OrderEventEndpoint;
use App\Services\Api\Endpoints\PartnerEndpoint;
use App\Services\Api\Endpoints\RewardAcquireEndpoint;
use App\Services\Api\Endpoints\RewardItemEndpoint;
use App\Services\Api\Endpoints\RewardsEndpoint;
use App\Services\Api\Endpoints\TransactionCancelPromocodeEndpoint;
use App\Services\Api\Endpoints\TransactionItemEndpoint;
use App\Services\Api\Endpoints\TransactionsEndpoint;
use App\Services\Api\Endpoints\UserBonusEndpoint;
use App\Services\Api\Endpoints\UserBonusExtendedEndpoint;
use App\Services\Api\Endpoints\UserCardItemEndpoint;
use App\Services\Api\Endpoints\UserCardsEndpoint;
use App\Services\Api\Endpoints\UserItemEndpoint;
use App\Services\Api\Endpoints\UsersEndpoint;
use App\Services\Api\Endpoints\UserSmsSendEndpoint;
use App\Services\Api\Endpoints\UserSmsVerifyEndpoint;
use App\Services\Api\Endpoints\UsersSearchEndpoint;
use App\Services\Api\Endpoints\UserTransactionsEndpoint;
use Illuminate\Support\ServiceProvider;
use App\Services\Api\Endpoints\ActionsEndpoint;
use App\Services\Api\Definitions\ActionDefinition;

class ApiDocsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        ApiDocsGenerator::registerEndpoints([
            ActionsEndpoint::class, CustomBonusActionsEndpoint::class, ActionItemEndpoint::class,
            CodesEndpoint::class, CodeItemEndpoint::class,
            RewardsEndpoint::class, RewardItemEndpoint::class, RewardAcquireEndpoint::class,
            TransactionsEndpoint::class, TransactionItemEndpoint::class, TransactionCancelPromocodeEndpoint::class,
            ChargeCouponEndpoint::class, PartnerEndpoint::class,
            UsersEndpoint::class, UserItemEndpoint::class,
            UserBonusEndpoint::class, UserCardsEndpoint::class, UserCardItemEndpoint::class, UserTransactionsEndpoint::class,
            UserBonusExtendedEndpoint::class, UserCardsEndpoint::class, UserCardItemEndpoint::class, UserTransactionsEndpoint::class,
            UserSmsSendEndpoint::class, UserSmsVerifyEndpoint::class,
            CheckCouponEndpoint::class, UsersSearchEndpoint::class,
            OrderEventEndpoint::class, CustomEventEndpoint::class,
        ]);

        ApiDocsGenerator::registerDefinitions([
            ActionDefinition::class, CouponDefinition::class,
            CodeDefinition::class, PartnerDefinition::class,
            RewardDefinition::class, TransactionDefinition::class,
            UserDefinition::class, SmsVerificationResultDefinition::class,
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
