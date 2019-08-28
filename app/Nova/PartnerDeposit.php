<?php

namespace App\Nova;

use App\Models\PartnerDeposit as PartnerDepositModel;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property PartnerDepositModel $resource
 */
class PartnerDeposit extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = PartnerDepositModel::class;

    public static $displayInNavigation = false;

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return __('Deposits');
    }

    public static function singularLabel(): string
    {
        return __('Deposit');
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->where('partner_id', $request->user()->partner->id);

        return parent::indexQuery($request, $query);
    }

    public function fields(Request $request): array
    {
        return [
            ID::make('ID')->sortable(),
            Select::make(__('Status'), 'status')
                ->options(PartnerDepositModel::getStatusList())
                ->displayUsing(function ($status) {
                    return PartnerDepositModel::getStatusList()[$status] ?? $status;
                }),
            Number::make(__('Amount'), 'amount')->displayUsing(function ($amount) {
                return \HAmount::fMedium($amount, $this->resource->currency);
            }),
            Number::make(__('Fee'), 'fee')->displayUsing(function ($fee) {
                return \HAmount::fMedium($fee, $this->resource->currency);
            }),
            DateTime::make(__('Created'), 'created_at'),
            DateTime::make(__('Updated'), 'updated_at'),
            DateTime::make(__('Confirmed'), 'confirmed_at'),
        ];
    }
}
