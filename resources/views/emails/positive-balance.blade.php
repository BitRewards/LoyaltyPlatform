<?php
/**
 * @var $link
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
    $points = $bitrewardsEnabled ? HAmount::floor($user->balance) : $user->balance;
    $str = __('Your balance is %s', '<span>' . HAmount::points($points) . '</span>');
    ?>
    @include ('emails.templates.notification.heading' , [
        'heading' => $str,
        'level' => 'h1',
    ])

    @include('emails.templates.notification.contentStart')


    <p>
        <?= HCustomizations::balanceChangedEmailHeading($user->partner) ?>
    </p>
    <br/>
    <p>
        <?php if ($bitrewardsEnabled): ?>
            <?= __("We kindly remind you that you have accumulated points in the loyalty program of the store \"%s%\". You can redeem points for special offers from the store or get more points for your purchases and friends' purchases.", $user->partner->title); ?>
        <?php else: ?>
            <?= __("We remind you that you have accumulated BIT tokens in the store loyalty program \"%s%\". You can redeem BIT tokens for special offers from the store, withdraw them to your external wallet or get more BIT tokens for your purchases and friends' purchases.", $user->partner->title); ?>
        <?php endif; ?>
    </p>
    @include('emails.templates.notification.contentEnd')
    <?php $str = HContext::isBitrewardsEnabled() ? __("Spend BIT Tokens") : __('Redeem points'); ?>
    @include('emails.templates.notification.button', [
        'title' => $str,
    ])

    <tr>
        <td>

            <p class="small">
                <br>
                <?php if ($user->partner->isAuthMethodPhone() && $user->phone) { ?>
                    <?= __("Your balance is tied to this phone: %s", $user->phone) ?>.
                <?php } elseif ($user->partner->isAuthMethodEmail() && $user->email) { ?>
                    <?= __("Your balance is tied to this email: %s", $user->email) ?>.
                <?php } ?>
            </p>
        </td>
    </tr>

    <?php
    if ($processor = $user->partner->getOrderReferralActionProcessor()) {
        $action = $processor->getAction();

        $data = null;

        if ($action->value_type === \App\Models\Action::VALUE_TYPE_PERCENT) {
            $data = HAction::getPercentageActionValueData($action);
        } elseif ($action->value_type === \App\Models\Action::VALUE_TYPE_FIXED_FIAT) {
            $data = HAction::getFiatPointsActionValueData($action);
        } else {
            $data = HAction::getFixedActionValueData($action);
        }
    ?>
    @include('emails.templates.notification.contentStart')
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td height="30" style="height: 30px;">
            </td>
        </tr>
        <?php $str = HContext::isBitrewardsEnabled() ? "More friends — more BIT!" : __('Invite more, earn more!'); ?>
        @include ('emails.templates.notification.heading', [
            'heading' => $str,
            'level' => 'h2',
        ])
        <tr>
            <td class="article-content">
                <?= HCustomizations::referralEmailBlockContent($user->partner) ?>
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
                    <?= HHtml::preventUrlFromEmailAutoUnderline($user->referral_link) ?>
                </big>
            </td>
        </tr>
        <tr>
            <td height="24" style="height: 24px;">
            </td>
        </tr>
        <tr>
            <td class="article-content">
                1. <?= HCustomizations::referralEmailBlockFirstStep($user->partner) ?>
            </td>
        </tr>
        <tr>
            <td class="article-content">
                2. <?= HCustomizations::referralEmailBlockSecondStep($user->partner, $data) ?>
            </td>
        </tr>
        <tr>
            <td class="article-content">
                3. <?= HCustomizations::referralEmailBlockThirdStep($user->partner, $processor) ?>
            </td>
        </tr>
    </table>



    @include('emails.templates.notification.contentEnd')

    <?php } ?>




@stop
