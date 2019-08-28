<?php

namespace App\Db\Factory\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\PartnerGroup getPartnerGroup(array $states = [], string $name = 'default')
 * @method static \Closure lazyPartnerGroupId(array $states = [], string $name = 'default')
 * @method static \App\Models\Partner getPartner(array $states = [], string $name = 'default')
 * @method static \Closure lazyPartnerId(array $states = [], string $name = 'default')
 * @method static \App\Models\Person getPerson(array $states = [], string $name = 'default')
 * @method static \Closure lazyPersonId(array $states = [], string $name = 'default')
 * @method static \App\Models\User getUser(array $states = [], string $name = 'default')
 * @method static \Closure lazyUserId(array $states = [], string $name = 'default')
 * @method static \App\Models\Action getAction(array $states = [], string $name = 'default')
 * @method static \Closure lazyActionId(array $states = [], string $name = 'default')
 * @method static \App\Models\Action  getReward(array $states = [], string $name = 'default')
 * @method static \Closure            lazyRewardId(array $states = [], string $name = 'default')
 * @method static \App\Models\Action  getAdministrator(array $states = [], string $name = 'default')
 * @method static \Closure            lazyAdministratorId(array $states = [], string $name = 'default')
 */
class Registry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'registry';
    }
}
