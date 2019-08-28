@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            <span class="">{{__('Buy BIT for ETH')}}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url('admin/wallet') }}" class="text-capitalize">{{ __('Wallet') }}</a></li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
            <div class="box">

                <div class="box-body row">
                    <!-- load the view from the application if it exists, otherwise load the one in the package -->
                    <div class="form-group col-md-12" style="">
                        <label>{{__('From')}}</label>
                        <input type="text" name="address" value="<?= $from_address ?>" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-12" style="">
                        <label>{{__('To')}}</label>
                        <input type="text" name="address" value="<?= $to_address ?>" class="form-control" readonly>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">

                    <div id="saveActions" class="form-group">

                        <div class="btn-group">

                            <a  href="{{ route('admin.wallet.index') }}" class="btn btn-success">
                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                <span>{{ __('I Sent') }}</span>
                            </a>

                        </div>
                    </div>

                </div><!-- /.box-footer-->

            </div><!-- /.box -->
        </div>
    </div>

@endsection

@section('after_styles')
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/create.css') }}">
@endsection


