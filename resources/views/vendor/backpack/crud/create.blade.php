@extends('backpack::layout')

@section('header')
	<section class="content-header">
	  <h1>
		  <span class=""><?= mb_convert_case($crud->entity_name, MB_CASE_TITLE) ?></span> <small><?= __("add form") ?></small>
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
	    <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
	    <li class="active">{{ trans('backpack::crud.add') }}</li>
	  </ol>
	</section>
@endsection

@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<!-- Default box -->
		@if ($crud->hasAccess('list'))
			<a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}</a><br><br>
		@endif

		@include('crud::inc.grouped_errors')

		  {!! Form::open(array('url' => $crud->route, 'method' => 'post', 'files'=>$crud->hasUploadFields('create'))) !!}
		  <div class="box">

		    <div class="box-body row">
		      <!-- load the view from the application if it exists, otherwise load the one in the package -->
		      @if(view()->exists('vendor.backpack.crud.form_content'))
		      	@include('vendor.backpack.crud.form_content', ['fields' => $crud->getFields('create')])
		      @else
		      	@include('crud::form_content', ['fields' => $crud->getFields('create')])
		      @endif
		    </div><!-- /.box-body -->
		    <div class="box-footer">

                @include('crud::inc.form_save_buttons')

		    </div><!-- /.box-footer-->

		  </div><!-- /.box -->
		  {!! Form::close() !!}
	</div>
</div>

@endsection


@section('after_styles')
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/create.css') }}">
@endsection

@section('after_scripts')
	<script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/create.js') }}"></script>
@endsection