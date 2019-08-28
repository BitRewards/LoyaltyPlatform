<?php

namespace App\Nova;

use App\Administrator;
use App\DTO\ReferrerSummaryData;
use App\Models\User;
use App\Services\ReferralStatisticService;
use Bitrewards\ReferralTool\Filters\FromDateFilter;
use Bitrewards\ReferralTool\Filters\ReferralGroupFilter;
use Bitrewards\ReferralTool\Filters\ToDateFilter;
use Bitrewards\ReferralTool\Filters\ValueRange;
use Bitrewards\ReferralTool\Metrics\ReferrerEarningTrend;
use Bitrewards\ReferralTool\Metrics\ReferrerReferralsPurchaseTrend;
use Bitrewards\ReferralTool\Metrics\ReferrerWithdrawsValue;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property User $resource
 */
class Referrer extends Resource
{
    /**
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * @var array
     */
    public static $search = [
        'id',
        'email',
        'phone',
    ];

    /**
     * @var ReferrerSummaryData
     */
    protected $referrerSummary;

    public function title()
    {
        return $this->resource->email
            ?? $this->resource->phone
            ?? $this->resource->id;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        /** @var Administrator $user */
        $user = $request->user();

        $subQuery = User::query()
            ->select('users.id', \DB::raw('MAX(person.last_visited_at) as referral_last_visited_at'))
            ->join('users as referrals', 'users.id', 'referrals.referrer_id')
            ->join('persons as person', 'referrals.person_id', 'person.id')
            ->where('users.partner_id', $user->partner->id)
            ->groupBy('users.id');

        $query = parent::indexQuery($request, $query)
            ->select('users.*', 'last_visited_at', 'referral_last_visited_at')
            ->join('persons as person', 'users.person_id', 'person.id')
            ->joinSub($subQuery, 'referral', function (JoinClause $join) {
                $join->type = 'left';
                $join->on('users.id', 'referral.id');
            })
            ->where('partner_id', $user->partner_id);

        return $query;
    }

    public static function label(): string
    {
        return __('Referrers');
    }

    public static function singularLabel(): string
    {
        return __('Referrer');
    }

    protected function getReferrerSummary(): ?ReferrerSummaryData
    {
        if (!$this->resource) {
            return null;
        }

        if (!$this->referrerSummary) {
            $this->referrerSummary = app(ReferralStatisticService::class)
                ->getReferrerSummary($this->resource);
        }

        return $this->referrerSummary;
    }

    public function authorizedToAdd(NovaRequest $request, $model)
    {
        return true;
    }

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Name'), 'name')->sortable(),
            Text::make(__('Email'), 'email')->sortable(),
            Text::make(__('Phone number'), 'phone'),
            Text::make(__('Balance'), 'balance')->sortable(),

            Text::make(__('Total number of referral purchases'), function () {
                return $this->getReferrerSummary()->referralPurchaseCount;
            })->hideFromIndex(),

            Text::make(__('Total amount of earnings'), function () {
                return $this->getReferrerSummary()->earningAmount;
            })->hideFromIndex(),

            Text::make(__('Total amount of withdraws'), function () {
                return $this->getReferrerSummary()->withdrawAmount;
            })->hideFromIndex(),

            BelongsTo::make(__('Referrer'), 'referrer', __CLASS__)->onlyOnDetail(),
            Text::make(__('Referral link'), 'referral_link')->hideFromIndex(),
            Text::make(__('Referral promo code'), 'referral_promo_code')->hideFromIndex(),
            Text::make(__('Signup type'), 'signup_type')->hideFromIndex(),

            DateTime::make(__('Last seen'), 'person.last_visited_at')->sortable(),

            DateTime::make(__('Last referral visit date'), 'referral_last_visited_at')
                ->resolveUsing(function ($value) {
                    return $value ? Carbon::make($value) : null;
                })->sortable(),

            HasMany::make(__('Referrals'), 'referrals', Referrer::class),

            HasMany::make(__('Referrals orders'), 'storeEntities', ReferralOrder::class),

            HasMany::make(__('Withdraws'), 'transactions', ReferrerWithdraw::class),
        ];
    }

    public function cards(Request $request): array
    {
        return [
            new ReferrerEarningTrend(),
            new ReferrerReferralsPurchaseTrend(),
            new ReferrerWithdrawsValue(),
        ];
    }

    public function filters(Request $request): array
    {
        return [
            new ReferralGroupFilter(),
            new ValueRange('balance', __('Balance')),
            new FromDateFilter('users.created_at', __('Registration date from')),
            new ToDateFilter('users.created_at', __('Registration date to')),
            new FromDateFilter('last_visited_at', __('Last visit from')),
            new ToDateFilter('last_visited_at', __('Last visit to')),
            new FromDateFilter('referral_last_visited_at', __('Referral last visit from')),
            new ToDateFilter('referral_last_visited_at', __('Referral last visit to')),
        ];
    }
}
