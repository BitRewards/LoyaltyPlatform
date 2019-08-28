<?php
/**
 * @var
 */
?>
@extends('emails.templates.sunny')

@section('content')

    @include ('emails.templates.sunny.heading' , [
        'heading' => __('Welcome!'),
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')
    <?php if ($user->partner->isFiatReferralEnabled()): ?>
        <p><?= __("If you haven't registered in the <b>«%s» referral program</b> - simply ignore this email", $user->partner->title); ?>.</p>
    <?php else: ?>
        <p><?= __("If you haven't registered in the <b>«%s» rewards program</b> - simply ignore this email", $user->partner->title); ?>.</p>
    <?php endif; ?>

    @include('emails.templates.sunny.contentEnd')

    <?php
    $text = __('Confirm email');
    ?>
    @include('emails.templates.sunny.button', [
        'title' => $text,
    ])

@stop