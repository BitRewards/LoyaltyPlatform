<!-- text input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    <textarea
    	name="{{ $field['name'] }}"
        data-json-editor
        @include('crud::inc.field_attributes')
    >{{ ($value = (old($field['name']) ?: (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )))) ? (!is_scalar($value) ? HJson::encode($value) : $value) : null }}</textarea>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>