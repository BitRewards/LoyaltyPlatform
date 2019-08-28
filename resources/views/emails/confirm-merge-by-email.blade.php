<?php
/**
 * @var $link
 */
?>
@extends('emails.templates.sunny')

@section('content')

@include('emails.templates.sunny.contentStart')

<p>
    <?= __("Hello, <br> In order to confirm your email in the loyalty program «%s», use this code", $partner->title) ?>
    <br><br>
    <h1><?= $token ?></h1>
</p>

@include('emails.templates.sunny.contentEnd')

@stop