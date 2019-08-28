<!-- Tiny MCE -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    <textarea
    	id="tinymce-{{ $field['name'] }}"
        name="{{ $field['name'] }}"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control tinymce'])
        >{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}</textarea>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <!-- include tinymce js-->
    <script src="{{ asset('vendor/backpack/tinymce/tinymce.min.js') }}"></script>
    {{-- <script src="{{ asset(config('backpack.base.route_prefix').'/js/vendor/tinymce/jquery.tinymce.min.js') }}"></script> --}}

    <script type="text/javascript">
    tinymce.init({
        selector: "textarea.tinymce",
        skin: "dick-light",
        plugins: "image,link,media,anchor",
        file_browser_callback : elFinderBrowser,
     });

    function elFinderBrowser (field_name, url, type, win) {
      tinymce.activeEditor.windowManager.open({
        file: '{{ url(config('backpack.base.route_prefix').'/elfinder/tinymce4') }}',// use an absolute path!
        title: 'elFinder 2.0',
        width: 900,
        height: 450,
        resizable: 'yes'
      }, {
        setUrl: function (url) {
          win.document.getElementById(field_name).value = url;
        }
      });
      return false;
    }
    </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}