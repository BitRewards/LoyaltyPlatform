@section('after_scripts')
    <script>/*
                JSONEditor.defaults.editors.object.options.collapsed = true;
                        JSONEditor.defaults.options.theme = 'bootstrap3';
                        JSONEditor.defaults.options.iconlib = 'fontawesome4';

                var el = $('[name="customizations"]');
                var json = el.val();
                // el.prev().remove();
    el.after('<input type="hidden" name="customizations">');
    var input = $('[name="customizations"]');

    el.replaceWith('<div id="customizations"></div>');
                        el = $('#customizations');
                var editor = new JSONEditor(el.get(0),{
                    schema: {
                        type: 'object',
                        title: ' ',
                        properties: {
                            'name': { 'type': 'string' },
                            'primary-color': { 'type': 'string', 'format': 'color' }
                        },
                    }
                });
editor.setValue(JSON.parse(json));

    editor.on('change',function() {
        var errors = editor.validate();
        if (!errors.length) {
                input.val(JSON.stringify(editor.getValue(), null, 2));
        }
    });*/
    </script>
@endsection

@extends('vendor.backpack.crud.edit')

