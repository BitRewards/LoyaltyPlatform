@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    @include('emails.templates.notification.contentStart')

    <p style="margin-bottom: 30px;">{{ __('We inform you that your employee has added bonuses to the client manually.') }}</p>

    @if (!is_null($actor))
        <p><strong>{{ __('Employee:') }}</strong> <a href="{{ url('/admin/user/'.$actor->id) }}">{{ $actor->getTitle() }}</a></p>
    @endif

    <p><strong>{{ __('Receiver:') }}</strong> <a href="{{ url('/admin/user/'.$receiver->id) }}">{{ $receiver->getTitle() }}</a></p>
    <p><strong>{{ __('Bonus Points:') }}</strong> {{ $bonus }}</p>
    @if (!is_null($comment))
        <p><strong>{{ __('Comment:') }}</strong> {{ $comment }}</p>
    @endif

    @include('emails.templates.notification.contentEnd')

    @include('emails.templates.notification.button', [
        'title' => __('View Recent Transactions'),
        'link' => url('/admin/transaction'),
    ])
@endsection
