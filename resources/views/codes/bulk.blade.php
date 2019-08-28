@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            {{ __('Promo codes')}}
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {!! Form::open(['url' => route('admin.code.storeBulk'), 'method' => 'post']) !!}
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Bulk code add')}}</h3>
                </div>
                <div class="box-body row">
                    @if (\Auth::user()->can('admin'))
                        @include('vendor.backpack.crud.fields.select2', ['field' => [
                            'label' => __('Partner'),
                            'model' => \App\Models\Partner::class,
                            'name' => 'partner_id',
                            'attribute' => 'title'
                        ]])
                    @else
                        @include('vendor.backpack.crud.fields.hidden', ['field' => ['value' => \Auth::user()->partner_id, 'name' => 'partner_id']])
                    @endif
                    @include('vendor.backpack.crud.fields.text', ['field' => ['label' => __('Amount'), 'name' => 'bonus_points']])
                    @include('vendor.backpack.crud.fields.textarea', ['field' => ['label' => __('Codes'), 'name' => 'tokens']])
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">
                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> {{ __('Add') }}
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('after_styles')
    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')
@endsection

@section('after_scripts')
    <!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
    @stack('crud_fields_scripts')
@endsection
