@if ($crud->hasAccess('export'))
    <a href="{{ url($crud->route.'/export') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-download"></i> <?= __("Export") ?></span></a>
@endif