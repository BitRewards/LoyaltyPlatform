@if ($crud->hasAccess('users_bulk_import'))
    <a href="{{ route('admin.user.createBulk') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Import') }}</a>
@endif