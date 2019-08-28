<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
     <input
        type="text"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($id) ? call_user_func($field['callback'], $id) : '') }}"
        @include('crud::inc.field_attributes')
     >

    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
