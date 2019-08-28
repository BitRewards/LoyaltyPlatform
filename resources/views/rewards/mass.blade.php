@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            {{ __('Rewards')}}
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {!! Form::open(['url' => route('admin.rewards.storeMassAward'), 'method' => 'post']) !!}
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Mass reward')}}</h3>
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
                    @include('vendor.backpack.crud.fields.text', ['field' => ['label' => __('Amount'), 'name' => 'points']])
                    @include('vendor.backpack.crud.fields.checkbox', ['field' => ['label' => __('Only verified emails'), 'name' => 'onlyConfirmedEmails']])
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">
                        <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> {{ __('Award') }}
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
