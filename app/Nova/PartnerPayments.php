<?php

namespace App\Nova;

use App\Models\Partner;
use App\Models\Reward;
use App\Models\Transaction;
use App\Services\PartnerService;
use App\Services\RewardService;
use App\Services\TransactionService;
use Bitrewards\ReferralTool\Cards\StaticValue;
use Bitrewards\ReferralTool\Fields\ButtonGroup;
use Bitrewards\ReferralTool\Fields\Hidden;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property Transaction $resource
 */
class PartnerPayments extends Resource
{
    /**
     * @var string
     */
    public static $model = Transaction::class;

    public static $displayInNavigation = false;

    public static $search = [
        'id',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query
            ->select('transactions.*')
            ->join('rewards', 'rewards.id', 'transactions.reward_id')
            ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW)
            ->where('transactions.partner_id', $request->user()->partner_id);
    }

    public static function label(): string
    {
        return __('Payments');
    }

    public static function singularLabel(): string
    {
        return __('Payment');
    }

    public function fields(Request $request): array
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $fiatWithdrawReward = app(RewardService::class)->getFiatWithdrawReward($partner);
        $partnerService = app(PartnerService::class);

        return [
            ID::make(),
            Select::make(__('Status'), 'status')
                ->options(\HTransaction::getStatuses())
                ->displayUsing(function () {
                    return \HTransaction::getStatusStr($this->resource);
                })->exceptOnForms(),

            Number::make(__('Amount'), 'data->fiatWithdrawAmount')
                ->displayUsing(function ($amount) {
                    return \HAmount::novaAmountFormat($amount);
                })
                ->exceptOnForms(),

            BelongsTo::make(mb_ucfirst(__('referrer')), 'user', Referrer::class)
                ->searchable()
                ->onlyOnForms(),

            Number::make(__('Withdrawal amount'), 'balance_change')
                ->fillUsing(static function (Request $request, Transaction $transaction) {
                    $transaction->balance_change = -abs($request->get('balance_change'));
                })
                ->rules('required')
                ->onlyOnForms(),

            Number::make(__('Fee'), 'data->fiatWithdrawFee')
                ->fillUsing(static function (Request $request, Transaction $transaction) use ($partner, $partnerService) {
                    $withdrawAmount = abs($request->get('balance_change'));
                    $fee = $request->get('data->fiatWithdrawFee');
                    $transaction->setAttribute('data->fiatWithdrawFeeType', 'fixed');

                    if (null === $fee) {
                        $fee = $partnerService->calculateFiatWithdrawFeeAmount($partner, $withdrawAmount);
                        $transaction->setAttribute('data->fiatWithdrawFeeType', 'percent');
                    }
                    $transaction->setAttribute('data->fiatWithdrawFee', $fee);
                    $transaction->setAttribute('data->fiatWithdrawAmount', $withdrawAmount - $fee);
                })
                ->displayUsing(function ($amount) {
                    return \HAmount::novaAmountFormat($amount);
                }),

            Number::make(__('Final Amount'), function () {
                return \HAmount::novaAmountFormat(abs($this->resource->balance_change));
            }),

            BelongsTo::make(__('Referrer'), 'user', Referrer::class)
                ->exceptOnForms(),

            DateTime::make(__('Created'), 'created_at')
                ->exceptOnForms(),

            DateTime::make(__('Confirmed'), 'confirmed_at')
                ->exceptOnForms(),

            Text::make(__('Comment'), 'data->fiatWithdrawComment')->exceptOnForms(),

            ButtonGroup::make(
                ButtonGroup::button(__('Confirm'), static function (Transaction $transaction) {
                    return route('referral.payment.confirm', ['transaction' => $transaction->id]);
                }, static function (Transaction $transaction) {
                    return Transaction::STATUS_PENDING !== $transaction->status;
                }),

                ButtonGroup::button(__('Reject'), static function (Transaction $transaction) {
                    return route('referral.payment.reject', ['transaction' => $transaction->id]);
                }, static function (Transaction $transaction) {
                    return Transaction::STATUS_PENDING !== $transaction->status;
                })
            )->exceptOnForms(),

            Text::make(__('First name'), 'data->fiatWithdrawFirstName')
                ->rules('required')
                ->hideFromIndex(),

            Text::make(__('Second name'), 'data->fiatWithdrawLastName')
                ->rules('required')
                ->hideFromIndex(),

            Text::make(__('Credit card'), 'data->fiatWithdrawCardNumber')->hideFromIndex(),

            Textarea::make(__('Comment'), 'data->fiatWithdrawComment')
                ->rows(3)
                ->onlyOnForms(),

            Hidden::make('status')
                ->withValue(Transaction::STATUS_PENDING)
                ->onlyOnForms(),

            Hidden::make('partner_id')
                ->withValue($partner->id),

            Hidden::make('type')
                ->withValue(Transaction::TYPE_REWARD),

            Hidden::make('reward_id')
                ->withValue($fiatWithdrawReward->id ?? null),
        ];
    }

    public function cards(Request $request): array
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $transactionService = app(TransactionService::class);
        $withdrawAmount = $transactionService->getPartnerWithdrawRequestsAmount($partner);
        $withdrawCount = $transactionService->getPartnerWithdrawRequestsCount($partner);
        $requiredAmount = $transactionService->getRequiredAmount($partner);

        return [
            new StaticValue(__('Amount of withdrawals requests'), $withdrawAmount),
            new StaticValue(__('Number of withdrawals requests'), $withdrawCount),
            (new StaticValue(__('Amount of available withdrawals'), $requiredAmount))
                ->withMeta([
                    'suffix' => \HAmount::labelMedium($partner->currency),
                ]),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return true;
    }
}
