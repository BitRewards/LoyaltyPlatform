@extends('backpack::layout')

@section('content-header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::crud.preview') }} <span class="text-lowercase">{{ $crud->entity_name }}</span>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
        <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
        <li class="active">{{ trans('backpack::crud.preview') }}</li>
      </ol>
    </section>
@endsection

@section('content')
    @if ($crud->hasAccess('list'))
        <a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} </a><br><br>
    @endif

    <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {!! __("User ") . "<b>" . $entry->name . "</b>" !!}
          </h3>
        </div>
        <div class="box-body">
          <table class="table table-bordered">

          </table> 
        </div><!-- /.box-body -->
      </div><!-- /.box -->

@endsection
