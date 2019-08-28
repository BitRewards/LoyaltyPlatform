<?php
/**
 * @var $link
 */
?>
@extends('emails.templates.sunny')

@section('content')

    @include ('emails.templates.sunny.heading' , [
        'heading' => __('Password restore request'),
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')

    <p><?= __("Simply delete this email if you did not request a password reset!") ?>.</p>

    @include('emails.templates.sunny.contentEnd')

    @include('emails.templates.sunny.button', [
        'title' => __('Recover password'),
    ])

@stop