@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            <span class="">{{__('Bitrewards settings')}}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ route('admin.wallet.settings') }}" class="text-capitalize">{{ __('Bitrewards settings') }}</a></li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
            @include('crud::inc.grouped_errors')

            {!! Form::open(array('url' => $crud->route, 'method' => 'post')) !!}
            <div class="box">

                <div class="box-body row">
                    <!-- load the view from the application if it exists, otherwise load the one in the package -->
                    @if(view()->exists('vendor.backpack.crud.form_content'))
                        @include('vendor.backpack.crud.form_content', ['fields' => $crud->getFields('create'), 'action' => 'create'])
                    @else
                        @include('crud::form_content', ['fields' => $crud->getFields('create'), 'action' => 'create'])
                    @endif
                </div><!-- /.box-body -->
                <div class="box-footer">

                    <div id="saveActions" class="form-group">

                        <input type="hidden" name="save_action" value="{{ $saveAction['active']['value'] }}">

                        <div class="btn-group">

                            <button type="submit" class="btn btn-success">
                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                <span>{{ __('Save') }}</span>
                            </button>

                        </div>

                        <a href="{{ route('admin.wallet.settings') }}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
                    </div>

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


