@extends('emails.templates.notification.notification', [
    'logo' => [
        'path' => HCustomizations::logoPicture($user->partner),
        'height' => 120,
    ]
])

@section('content')
    @include('emails.templates.notification.contentStart')
    <p>{{ __('The following users received more bonuses than defined by the limits.') }}</p>
    @if ($period === \App\Services\Alerts\BonusesOverflowAlert::PERIOD_LAST_WEEK)
        <p>{{ __('The report is shown for the week from %s to %s.', HDate::dateFull($start->timestamp), HDate::dateFull($end->timestamp)) }}</p>
    @else
        <p>{{ __('The report is shown for the last day.') }}</p>
    @endif
    <br>
    <p><strong>{{ __('Bonuses Limit: %s', $periodLimit) }}</strong></p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px">
        <thead>
            <tr style="text-align: left;">
                <th>{{ __('User') }}</th>
                <th>{{ __('Bonuses Given') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" style="height: 20px">&nbsp;</td>
            </tr>
            @foreach ($users as $u)
                <tr style="height: 30px">
                    <td>
                        <a href="{{ url('/admin/user/'.$u->id) }}">
                            {{ $u->getTitle() }}
                        </a>
                    </td>
                    <td><strong>{{ $balances->get($u->id) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('emails.templates.notification.contentEnd')
@endsection
