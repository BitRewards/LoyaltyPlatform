@if ($crud->hasAccess('delete'))
	<a href="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-button-type="delete" data-tooltip="<?= __("Delete") ?>"><i class="fa fa-trash"></i>
	</a>
@endif