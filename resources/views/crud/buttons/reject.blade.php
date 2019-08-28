@if ($crud->hasAccess('reject'))
    <a href="{{ url($crud->route.'/reject/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-tooltip="{{ __('Cancel transaction')}}"><i class="fa fa-close"></i></a>
@endif