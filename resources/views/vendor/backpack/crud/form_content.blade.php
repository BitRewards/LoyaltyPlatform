@if ($crud->model->translationEnabled())
<input type="hidden" name="locale" value={{ $crud->request->input('locale')?$crud->request->input('locale'):App::getLocale() }}>
@endif

{{-- See if we're using tabs --}}
@if ($crud->tabsEnabled())
    @include('crud::inc.show_tabbed_fields')
@else
    @include('crud::inc.show_fields', ['fields' => $fields])
@endif

{{-- Define blade stacks so css and js can be pushed from the fields to these sections. --}}

@section('after_styles')
    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')
@endsection

@section('after_scripts')
    <!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
    @stack('crud_fields_scripts')

    <script>
    jQuery('document').ready(function($){

      // Save button has multiple actions: save and exit, save and edit, save and new
      var saveActions = $('#saveActions'),
      crudForm        = saveActions.parents('form'),
      saveActionField = $('[name="save_action"]');

      saveActions.on('click', '.dropdown-menu a', function(){
          var saveAction = $(this).data('value');
          saveActionField.val( saveAction );
          crudForm.submit();
      });

      // Ctrl+S and Cmd+S trigger Save button click
      $(document).keydown(function(e) {
          if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
          {
              e.preventDefault();
              // alert("Ctrl-s pressed");
              $("button[type=submit]").trigger('click');
              return false;
          }
          return true;
      });

      // Place the focus on the first element in the form
      @if( $crud->autoFocusOnFirstField )
        @php
          $focusField = array_first($fields, function($field) {
              return isset($field['auto_focus']) && $field['auto_focus'] == true;
          });
        @endphp

        @if ($focusField)
          window.focusField = $('[name="{{ $focusField['name'] }}"]').eq(0),
        @else
          var focusField = $('form').find('input, textarea, select').not('[type="hidden"]').eq(0),
        @endif

        fieldOffset = focusField.offset().top,
        scrollTolerance = $(window).height() / 2;

        focusField.trigger('focus');

        if( fieldOffset > scrollTolerance ){
            $('html, body').animate({scrollTop: (fieldOffset - 30)});
        }
      @endif

      // Add inline errors to the DOM
      @if ($crud->inlineErrorsEnabled() && $errors->any())

        window.errors = {!! json_encode($errors->messages()) !!};
        // console.error(window.errors);

        $.each(errors, function(property, messages){

            var field = $('[name="' + property + '"]'),
                container = field.parents('.form-group');

            container.addClass('has-error');

            $.each(messages, function(key, msg){
                // highlight the input that errored
                var row = $('<div class="help-block">' + msg + '</div>');
                row.appendTo(container);

                // highlight its parent tab
                @if ($crud->tabsEnabled())
                var tab_id = $(container).parent().attr('id');
                $("#form_tabs [aria-controls="+tab_id+"]").addClass('text-red');
                @endif
            });
        });

      @endif

      });
    </script>
@endsection
