<?php

namespace App\Nova;

use App\Administrator;
use App\Models\Action;
use App\Models\StoreEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property StoreEntity $resource
 */
class ReferralOrder extends Resource
{
    /**
     * @var string
     */
    public static $model = StoreEntity::class;

    public static $displayInNavigation = false;

    public static $search = [
        'id',
        'external_id',
    ];

    protected static function referralOrderQuery(NovaRequest $request, Builder $query): Builder
    {
        /** @var Administrator $user */
        $user = $request->user();

        return $query
            ->select('store_entities.*', 'transactions.balance_change')
            ->join('actions', 'transactions.action_id', 'actions.id')
            ->where('actions.type', Action::TYPE_ORDER_REFERRAL);
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $query = parent::indexQuery($request, $query);

        return self::referralOrderQuery($request, $query);
    }

    public static function label(): string
    {
        return __('Referral Orders');
    }

    public static function singularLabel(): string
    {
        return __('Referral Order');
    }

    public function fields(Request $request): array
    {
        return [
            ID::make(__('Order ID'), 'id'),

            Text::make(__('Order shop ID'), 'external_id'),

            DateTime::make(__('Order created date'), 'created_at'),

            Text::make(__('Order amount'), function () {
                return (float) $this->resource->data->amountTotal;
            }),

            Text::make(__('Reward amount'), function () {
                return (float) ($this->resource->transactions->first()->balance_change ?? 0);
            }),

            Text::make(__('Status'), 'status'),

            DateTime::make(__('Confirmed date'), 'confirmed_at'),

            Text::make(__('Referral name'), function () {
                return $this->resource->data->name;
            }),

            Text::make(__('Referral email'), function () {
                return $this->resource->data->email;
            }),

            Text::make(__('Referral phone'), function () {
                return $this->resource->data->phone;
            }),
        ];
    }
}
