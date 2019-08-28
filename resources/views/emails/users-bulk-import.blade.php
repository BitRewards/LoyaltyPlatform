<?php
/**
 * @var $link
 */
?>
@extends('emails.templates.sunny')

@section('content')
    @include ('emails.templates.sunny.heading' , [
        'heading' => __('We have processed your request. Result:'),
        'level' => 'h1',
    ])

    @include('emails.templates.sunny.contentStart')

    <p>
        - {!! __('Total records: <b>%count%</b>;', $report->total) !!}<br>
        - {!! __('Records skipped: <b>%count%</b>;', $report->skipped) !!}<br>
        - {!! __('New users created: <b>%count%</b>;', $report->created) !!}<br>
        - {!! __('Existing users updated: <b>%count%</b>.', $report->updated) !!}<br>
    </p>

    @include('emails.templates.sunny.contentEnd')

@stop