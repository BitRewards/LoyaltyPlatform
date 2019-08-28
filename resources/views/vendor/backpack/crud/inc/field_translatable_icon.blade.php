@if (isset($crud) && method_exists($crud->model, 'isTranslatableAttribute') && $crud->model->isTranslatableAttribute($field['name']) && config('backpack.crud.show_translatable_field_icon'))
<i class="fa fa-flag-checkered pull-{{ config('backpack.crud.translatable_field_icon_position') }}" style="margin-top: 3px;" title="This field is translatable."></i>
@endif