@if ($crud->hasAccess('login_as_partner'))
    <a href="{{ url('/admin/loginAsPartner', [$entry->getKey()]) }}" class="btn btn-xs btn-default" data-tooltip="Авторизоваться под партнером"><i class="fa fa-key"></i></a>
@endif