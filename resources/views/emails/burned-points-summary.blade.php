@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    @include('emails.templates.notification.contentStart')

    @if($isHaveBurnedTransactions && $isHaveExpiringTransactions)
        {{ __('Last week, the %burnedAmount% points you earned in the %store% loyalty program burned out. Your balance is %balance% points. In the next 14 days in the %store% loyalty program will burn %expiringAmount% your points. We recommend to spend points in the store and earn more!', [
            'burnedAmount' => $burnedAmount,
            'expiringAmount' => $expiringAmount,
            'store' => $store,
            'balance' => $balance,
        ]) }}
    @elseif($isHaveBurnedTransactions)
        {{ __('Last week, the %burnedAmount% points you earned in the %store% loyalty program burned out. Your balance is %balance% points. We recommend spending the remaining points in the store and earn more!', [
            'burnedAmount' => $burnedAmount,
            'store' => $store,
            'balance' => $balance,
        ]) }}
    @elseif($isHaveExpiringTransactions)
        {{ __('In the next 14 days, the %store% loyalty program will burn %expiringAmount% your loyalty points. We recommend to spend them on purchases or discount coupons!', [
            'store' => $store,
            'expiringAmount' => $expiringAmount,
        ]) }}
    @endif


    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="height:30px"></td>
        </tr>
        @foreach($transactions as $transaction)
        <?php /* @var App\Models\Transaction $transaction */ ?>
        <tr>
            <td height="9" colspan="4" style="height: 9px; ">
        </tr>
        <tr>
            <td width="50%" style="width: 50%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="block" width="130" style="color: #bebebe; font-size: 14px; width: 130px;">
                            <?= HDate::dateTimeFull($transaction->output_balance_expires_at ?: $transaction->created_at); ?>
                        </td>
                        <td class="block" style="color: #000; font-size: 14px;">
                            {{ HTransaction::getTitle($transaction) }}
                        </td>
                    </tr>
                </table>
            </td>

            <td width="50%" style="width: 50%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="block color-highlighted" style="width: 50%;  font-size: 14px; text-align: right;">
                            {{ \HAmount::points($transaction->output_balance ?: $transaction->balance_change, $partner) }}
                        </td>
                        <td class="block large_only" style="display: table-cell; width: 50%; padding-left: 10px; color: #bebebe; font-size: 14px; text-align: right;">
                            {{ \HTransaction::getStatusStr($transaction) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="9" colspan="4" style="height: 9px; border-bottom: 1px solid #dedede;">
        </tr>
        @endforeach
    </table>

    @include('emails.templates.notification.contentEnd')

    @include('emails.templates.notification.button', [
        'title' => \HContext::isBitrewardsEnabled() ? __('Spend BIT Tokens') : __('Redeem points'),
    ])
@endsection