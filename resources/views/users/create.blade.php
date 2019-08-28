@extends('crud::create')

@section('header')
    <section class="content-header">
        <h1>
            <span class=""><?= mb_convert_case($crud->entity_name, MB_CASE_TITLE); ?></span> <small><?= __("You can register new user here. Fill user's e-mail or mobile phone number and password."); ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.add') }}</li>
        </ol>
    </section>
@endsection