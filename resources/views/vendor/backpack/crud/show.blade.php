@extends('backpack::layout')

@section('content-header')
	<section class="content-header">
	  <h1>
		  <span class=""><?= mb_convert_case($crud->entity_name, MB_CASE_TITLE) ?> #<?= $entry->id ?></span> <small><?= __("preview") ?></small>
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
		<a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}</a><br><br>
	@endif

	<!-- Default box -->
	  <div class="box">
	    <div class="box-body">
	      {{ dump($entry) }}
	    </div><!-- /.box-body -->
	  </div><!-- /.box -->

@endsection


@section('after_styles')
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/show.css') }}">
@endsection

@section('after_scripts')
	<script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/show.js') }}"></script>
@endsection