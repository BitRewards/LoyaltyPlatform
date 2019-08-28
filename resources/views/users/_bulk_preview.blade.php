<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Balance') }}</th>
        </tr>
    </thead>
    <body>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->balance }}</td>
            </tr>
        @endforeach
    </body>
</table>

<div class="box-footer">
    <button type="button" class="btn btn-success js-bulk-import-button">
        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> {{  __("Import %count% {user|users|users}", count($users)) }}
    </button>
</div>