<?php
/**
 * @var $commentId
 */
?>
@extends('emails.templates.sunny')

@section('content')

    @include ('emails.templates.sunny.heading' , [
        'heading' => 'Hello!',
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')

    <p>Today will be a great day!</p>

    @include('emails.templates.sunny.contentEnd')

    @include('emails.templates.sunny.button', [
            'title' => 'Click me',
            'link' => 'http://google.com'
    ])

@stop