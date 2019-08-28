<?php
/**
 * @var App\Models\User $sender
 *
 */
$link = $sender->referral_link;

$rewardStr = HPartner::getOrderReferralRewardStr($partner, false);
?>
@extends('emails.templates.sunny')

@section('content')

    @include ('emails.templates.sunny.heading' , [
        'heading' => __("%s writes you:", $senderName),
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')

    <blockquote>
        <?= nl2br(htmlentities($inviteText)) ?>
    </blockquote>


    @include('emails.templates.sunny.contentEnd')

    @include('emails.templates.sunny.button', [
        'title' => __('Get %s discount', $rewardStr),
    ])
@stop

