@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    @include('emails.templates.notification.contentStart')
    <p>{{ __("Your ETH-wallet doesn't have sufficient funds for payment.") }}</p>
    @include('emails.templates.notification.contentEnd')
@endsection
