<?php
/**
 * @var $link
 */
?>
@extends('emails.templates.sunny')

@section('content')

@include('emails.templates.sunny.contentStart')

<p>
    <?= __("Hello, <br> Use this one-time code to sign in to BitRewards app:") ?>
    <br><br>
    <h1><?= $token ?></h1>
    <br><br>
    <small style="color: #999"><?= __("This code is only valid for 5 minutes") ?>.</small>
</p>

@include('emails.templates.sunny.contentEnd')

@stop