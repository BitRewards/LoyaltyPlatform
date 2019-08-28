@if ($crud->hasAccess('confirm'))
    <a href="{{ url($crud->route.'/confirm/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-tooltip="{{ __('Confirm transaction') }}"><i class="fa fa-check"></i></a>
@endif