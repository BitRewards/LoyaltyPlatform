<?php

namespace App\Nova;

use App\Models\Reward;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property Transaction $resource
 */
class ReferrerWithdraw extends Resource
{
    /**
     * @var string
     */
    public static $model = Transaction::class;

    public static $displayInNavigation = false;

    public static $search = [
        'id',
    ];

    protected static function referralWithdrawQuery(NovaRequest $request, Builder $query): Builder
    {
        return $query
            ->select('transactions.*')
            ->join('rewards', 'rewards.id', 'transactions.reward_id')
            ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW);
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query = parent::indexQuery($request, $query);

        return self::referralWithdrawQuery($request, $query);
    }

    public static function label(): string
    {
        return __('Referrer Withdraws');
    }

    public static function singularLabel(): string
    {
        return __('Referrer Withdraw');
    }

    public function fields(Request $request): array
    {
        return [
            ID::make(__('Payout ID'), 'id'),
            DateTime::make(__('Request date'), 'created_at'),

            Text::make(__('Amount of the payout'), function () {
                $data = $this->resource->data;

                return (float) ($data->fiatWithdrawAmount - $data->fiatWithdrawFee);
            }),

            Text::make(__('Fee'), function () {
                return (float) $this->resource->data->fiatWithdrawFee;
            }),

            Text::make(__('Total amount of the payout'), function () {
                return (float) $this->resource->data->fiatWithdrawAmount;
            }),

            Text::make(__('Card number'), function () {
                return $this->resource->data->fiatWithdrawCardNumber;
            }),

            Text::make(__('Recipient name'), function () {
                $data = $this->resource->data;

                return "{$data->fiatWithdrawFirstName} {$data->fiatWithdrawLastName}";
            }),

            Text::make(__('Status'), 'status'),
            DateTime::make(__('Payout / Cancel date'), function () {
                $date = $this->resource->confirmed_at ?: $this->resource->rejected_at;

                return $date ? $date->toAtomString() : null;
            }),
        ];
    }
}
