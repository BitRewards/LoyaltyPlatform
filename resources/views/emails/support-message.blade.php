<?php
/**
 * @var App\Models\User $sender
 *
 */
?>
@extends('emails.templates.sunny')

@section('content')

    @include ('emails.templates.sunny.heading' , [
        'heading' => __("%s writes:", ($sender->name ?: $sender->email)),
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')

    <blockquote>
        <?= nl2br(htmlentities($text)) ?>
    </blockquote>

    <p>
        <?= __("The answer will be sent to %email%", '<b>' . $desiredEmail . '</b>') ?>.
    </p>

    <p>
        <?= __("Rewards program user ID") ?>: {{ $sender->id }}, <?= __("the key of the user of loyalty ") ?> {{ $sender->key }}.
    </p>

    @include('emails.templates.sunny.contentEnd')

@stop

