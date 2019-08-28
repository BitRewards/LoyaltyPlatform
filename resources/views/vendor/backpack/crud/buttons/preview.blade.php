@if ($crud->hasAccess('show'))
	<a href="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-tooltip="Предпросмотр"><i class="fa fa-eye"></i></a>
@endif