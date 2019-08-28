<?php

namespace App\Providers;

use App\Administrator;
use App\Models\HelpItem;
use App\Models\PartnerDeposit;
use App\Models\SpecialOfferAction;
use App\Models\SpecialOfferReward;
use App\Models\StoreEntity;
use App\Models\Transaction;
use App\Policies\HelpItemPolicy;
use App\Policies\SpecialOfferPolicy;
use App\Models\User;
use App\Models\Action;
use App\Models\Reward;
use App\Models\Code;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\ActionPolicy;
use App\Policies\RewardPolicy;
use App\Policies\CodePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\Nova;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Transaction::class => TransactionPolicy::class,

        // just in case
        \App\User::class => UserPolicy::class,
        User::class => UserPolicy::class,
        Action::class => ActionPolicy::class,
        Reward::class => RewardPolicy::class,
        Code::class => CodePolicy::class,
        HelpItem::class => HelpItemPolicy::class,
        SpecialOfferAction::class => SpecialOfferPolicy::class,
        SpecialOfferReward::class => SpecialOfferPolicy::class,
    ];

    protected $novaPolicies = [
        User::class => Nova\UserPolicy::class,
        Transaction::class => Nova\TransactionPolicy::class,
        StoreEntity::class => Nova\StoreEntityPolicy::class,
        PartnerDeposit::class => Nova\PartnerDepositPolicy::class,
    ];

    public function registerNovaPolicies(): void
    {
        foreach ($this->novaPolicies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    public function isNovaRequest(): bool
    {
        return in_array(request()->segment(1), [ltrim(config('nova.path'), '/'), 'nova-api'], true);
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        if ($this->isNovaRequest()) {
            $this->registerNovaPolicies();
        } else {
            $this->registerPolicies();
        }

        \Gate::define('admin', function ($user) {
            return Administrator::ROLE_ADMIN == $user->role;
        });

        \Gate::define('partner', function ($user) {
            return Administrator::ROLE_PARTNER == $user->role || Administrator::ROLE_ADMIN == $user->role;
        });

        \Gate::define('partner-or-cashier', function ($user) {
            return $user->can('partner') || Administrator::ROLE_CASHIER === $user->role;
        });
    }
}
