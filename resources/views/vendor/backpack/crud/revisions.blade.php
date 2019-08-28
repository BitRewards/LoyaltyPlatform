@extends('backpack::layout')

@section('header')
  <section class="content-header">
    <h1>
      <span>{{ ucfirst($crud->entity_name) }}</span> {{ trans('backpack::crud.revisions') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(config('backpack.base.route_prefix'),'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
      <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
      <li class="active">{{ trans('backpack::crud.revisions') }}</li>
    </ol>
  </section>
@endsection

@section('content')
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <!-- Default box -->
    @if ($crud->hasAccess('list'))
      <a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}</a><br><br>
    @endif

    @if(!count($revisions))
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('backpack::crud.no_revisions') }}</h3>
        </div>
      </div>
    @else
      @include('crud::inc.revision_timeline')
    @endif
  </div>
</div>
@endsection


@section('after_styles')
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/revisions.css') }}">
@endsection

@section('after_scripts')
  <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/revisions.js') }}"></script>
@endsection