<?php
/**
 * @var
 * @var string          $currentBalanceStr
 * @var bool            $forceFiatForAllTransactions
 * @var App\Models\User $user
 * @var bool            $isWithdrawDisabled
 */
?>
@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    <?php
    $bitrewardsEnabled = $user->partner->isBitrewardsEnabled();
    $str = __('Your balance is %s', '<span>'.$currentBalanceStr.'</span>');

    if ($user->partner->isGradedPercentRewardModeEnabled()) {
        $str = __('Your available discount is  %s', '<span>'.HAmount::pointsToPercentFormatted($currentBalanceStr).'</span>');
    }
    ?>
    @include ('emails.templates.notification.heading' , [
        'heading' => $str,
        'level' => 'h1',
    ])

    @include('emails.templates.notification.contentStart')


    <p>
        <?= HCustomizations::balanceChangedEmailHeading($user->partner); ?>
    </p>

    <!--p><?= __('Your balance status update:'); ?></p-->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="height:30px"></td>
        </tr>
        <?php foreach ($user->getLastTransactions() as $transaction) {
        ?>
        <?php
        /**
         * @var App\Models\Transaction
         */
        ?>
        <tr>
            <td height="9" colspan="4" style="height: 9px; ">
        </tr>
        <tr>
            <td width="50%" style="width: 50%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="block" width="130" style="color: #bebebe; font-size: 14px; width: 130px;">
                            <?= HDate::dateTimeFull($transaction->created_at); ?>
                        </td>
                        <td class="block" style="color: #000; font-size: 14px;">
                            <?= HTransaction::getTitle($transaction); ?>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="50%" style="width: 50%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="block mobile_only" style="color: #bebebe; font-size: 14px; text-align: right;">
                            <?= HTransaction::getStatusStr($transaction); ?>
                        </td>
                        <td class="block <?=  !$transaction->isPending() ? ($transaction->balance_change > 0 ? 'color-highlighted' : 'color-black') : ''; ?>" style="width: 50%;  font-size: 14px; text-align: right;">
                            <?php if (!$transaction->partner->isGradedPercentRewardModeEnabled()): ?>
                                <?php
                                    if ($forceFiatForAllTransactions) {
                                        $transactionAmount = HAmount::fShort(
                                            HAmount::pointsToFiat($transaction->balance_change, $user->partner),
                                            $user->partner->currency
                                        );
                                    } else {
                                        $transactionAmount = HAmount::points(
                                            $bitrewardsEnabled ?
                                                HAmount::floor($transaction->balance_change) :
                                                $transaction->balance_change
                                        );
                                    } ?>
                                <?= $transaction->balance_change > 0 ? '+' : '-'; ?><?= $transactionAmount; ?>
                            <?php else: ?>
                                <?= $transaction->balance_change > 0 ? HAmount::pointsToPercentFormatted($transaction->balance_change) : ''; ?>
                            <?php endif; ?>
                        </td>
                        <td class="block large_only" style="display: table-cell; width: 50%; padding-left: 10px; color: #bebebe; font-size: 14px; text-align: right;">
                            <?= HTransaction::getStatusStr($transaction); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="9" colspan="4" style="height: 9px; border-bottom: 1px solid #dedede;">
        </tr>
        <?php
    } ?>
    </table>

    @include('emails.templates.notification.contentEnd')
    <?php
    if ($forceFiatForAllTransactions) {
        $buttonTitle = __('Withdraw my money');
    } else {
        $buttonTitle = HContext::isBitrewardsEnabled() ? __('Spend BIT Tokens') : __('Earn and redeem points');
    }

    if ($isWithdrawDisabled) {
        $buttonTitle = __('Watch my balance');
    }

    if ($user->partner->isGradedPercentRewardModeEnabled()) {
        $buttonTitle = __('Earn a discount');
    }

    ?>
    @include('emails.templates.notification.button', [
        'title' => $buttonTitle,
    ])

    <tr>
        <td>

            <p class="small">
                <br>
                <?php if ($user->partner->isAuthMethodPhone() && $user->phone) {
        ?>
                    <?= __('Your balance is tied to this phone: %s', $user->phone); ?>.
                <?php
    } elseif ($user->partner->isAuthMethodEmail() && $user->email) {
        ?>
                    <?= __('Your balance is tied to this email: %s', $user->email); ?>.
                <?php
    } ?>
            </p>
        </td>
    </tr>

    <?php

    if (!$user->partner->isGradedPercentRewardModeEnabled() && !$isWithdrawDisabled && $processor = $user->partner->getOrderReferralActionProcessor()) {
        $action = $processor->getAction();

        $data = null;

        if (\App\Models\Action::VALUE_TYPE_PERCENT === $action->value_type) {
            $data = HAction::getPercentageActionValueData($action);
        } elseif (\App\Models\Action::VALUE_TYPE_FIXED_FIAT === $action->value_type) {
            $data = HAction::getFiatPointsActionValueData($action);
        } else {
            $data = HAction::getFixedActionValueData($action);
        } ?>
    @include('emails.templates.notification.contentStart')
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td height="30" style="height: 30px;">
            </td>
        </tr>

        <?php $str = $forceFiatForAllTransactions ? __('Invite more, earn more money!') : (HContext::isBitrewardsEnabled() ? 'More friends — more BIT!' : __('Invite more, earn more!')); ?>

        @include ('emails.templates.notification.heading', [
            'heading' => $str,
            'level' => 'h2',
        ])


        <tr>
            <td class="article-content">
                <?= HCustomizations::referralEmailBlockContent($user->partner); ?>
            </td>
        </tr>
        <tr>
            <td height="24" style="height: 24px;">
            </td>
        </tr>
        <tr>
            <td>
                <big class="share" style='padding: 9px 0;
                    display: block;
                    font-size: 18px;
                    font-family: Helvetica Neue Bold, Helvetica, Arial, Helvetica, serif;
                    font-weight: bold;
                    color: #000;
                    text-align: center;
                    background: #f9f9f9;
                    border: 1px solid #dedede;
                    border-radius: 5px;
                '>
                    <?= HHtml::preventUrlFromEmailAutoUnderline($user->referral_link); ?>
                </big>
            </td>
        </tr>
        <tr>
            <td height="24" style="height: 24px;">
            </td>
        </tr>
        <tr>
            <td class="article-content">
                1. <?= HCustomizations::referralEmailBlockFirstStep($user->partner); ?>
            </td>
        </tr>
        <tr>
            <td class="article-content">
                2. <?= HCustomizations::referralEmailBlockSecondStep($user->partner, $data); ?>
            </td>
        </tr>
        <tr>
            <td class="article-content">
                3. <?= HCustomizations::referralEmailBlockThirdStep($user->partner, $processor); ?>
            </td>
        </tr>
    </table>



    @include('emails.templates.notification.contentEnd')

    <?php
    } ?>




@stop
