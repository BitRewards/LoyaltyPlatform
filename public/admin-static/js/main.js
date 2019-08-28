$(function(){
    $('[data-json-editor]').each(function(){

        var $this = $(this);
        var name = $this.attr('name');

        if (!name || $this.data('json-init')) {
            return;
        }
        $this.data('json-init', 1);

        var raw = $this.val(), success = false;
        if (raw) {
            try {
                raw = JSON.stringify(JSON.parse(raw), null, 2);
            } catch (e) {
            }
        }


        var $container = $('<div>').addClass('json-editor').insertBefore($this);
        var container = $container.get(0);
        $this.hide();

        function initJsonEditors() {
            var editor = ace.edit(container);
            editor.getSession().setMode("ace/mode/javascript");
            //editor.getSession().setMode("ace/mode/js");

            editor.setOptions({
                maxLines: 100,
                minLines: 2,
                autoScrollEditorIntoView: true,
                tabSize: 2
            });

            editor.setValue(raw, 1);

            editor.on('change', function(){
                setTimeout(function(){
                    $this.val(editor.getValue());
                    $this.trigger('change');
                }, 0);
            });
        }

        if (!window.ace) {
            var script = document.createElement("script");
            $('head').append(script);

            script.onload = initJsonEditors;
            script.src = "/admin-static/js/libs/ace/ace.js";

        } else {
            initJsonEditors();
        }
    });

});