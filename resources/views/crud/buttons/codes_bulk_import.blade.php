@if ($crud->hasAccess('codes_bulk_import'))
    <a href="{{ route('admin.code.createBulk') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{  __('Import') }}</a>
@endif