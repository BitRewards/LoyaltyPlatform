<!-- select2 from ajax multiple -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    <input type="hidden" name="{{ $field['name'] }}" id="select2_ajax_multiple_{{ $field['name'] }}"
        @if(isset($field['value']))
            value="{{ $field['value'] }}"
        @endif
    @include('crud::inc.field_attributes', ['default_class' =>  'form-control'])
    >

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

@php
    $connected_entity = new $field['model'];
    $connected_entity_key_name = $connected_entity->getKeyName();
@endphp

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('vendor/backpack/select2/select2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/backpack/select2/select2-bootstrap-dick.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('vendor/backpack/select2/select2.js') }}"></script>
    @endpush

@endif

<!-- include field specific select2 js-->
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        // trigger select2 for each untriggered select2 box
        $("#select2_ajax_multiple_{{ $field['name'] }}").each(function (i, obj) {
            if (!$(obj).data("select2"))
            {
                $(obj).select2({
                    multiple: true,
                    placeholder: "{{ $field['placeholder'] }}",
                    minimumInputLength: "{{ $field['minimum_input_length'] }}",
                    ajax: {
                        url: "{{ $field['data_source'] }}",
                        dataType: 'json',
                        quietMillis: 250,
                        data: function (term, page) {
                            return {
                                q: term, // search term
                                page: page
                            };
                        },
                        results: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: $.map(data.data, function (item) {
                                    textField = "{{$field['attribute']}}";
                                    return {
                                        text: item[textField],
                                        id: item["{{ $connected_entity_key_name }}"]
                                    }
                                }),
                                more: data.current_page < data.last_page
                            };
                        },
                        cache: true
                    },
                    initSelection: function(element, callback) {
                        // the input tag has a value attribute preloaded that points to a preselected repository's id
                        // this function resolves that id attribute to an object that select2 can render
                        // using its formatResult renderer - that way the repository name is shown preselected
                        $.ajax("{{ $field['data_source'] }}" + '/' + "{{ $field['value'] ? $field['value'] : 0 }}", {
                            dataType: "json"
                        }).done(function(data) {
                            textField = "{{$field['attribute']}}";
                            callback({ text: data[textField], id: data["{{ $connected_entity_key_name }}"] });
                        });
                    },
                });
            }
        });
    });
</script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}