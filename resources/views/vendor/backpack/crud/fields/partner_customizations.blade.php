@if (!empty($crud->entry))
    <div @include('crud::inc.field_wrapper_attributes') >
        <label>{!! $field['label'] !!}</label>
        <partner-customizations partner-id="{{ $crud->entry->id }}"></partner-customizations>
    </div>
@endif
