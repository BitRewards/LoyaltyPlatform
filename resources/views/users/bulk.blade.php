@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            {{ __('Users')}}
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {!! Form::open(['url' => route('admin.user.storeBulk'), 'method' => 'post', 'class' => 'js-bulk-form', 'enctype'=>'multipart/form-data']) !!}
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('Bulk users import')}}</h3>
                </div>
                <div class="box-body row">
                    @include('vendor.backpack.crud.fields.text', [
                       'field' => [
                           'label' => __('TItle'),
                           'name' => 'title',
                           'attributes' => ['placeholder' => __('The name of the bulk users import')]],
                    ])

                    @include('vendor.backpack.crud.fields.select2', ['field' => [
                        'label' => __('Mode'),
                        'name' => 'mode',
                        'options' => \App\Enums\UsersBulkImport\ImportMode::getLabels(),
                    ]])

                    @include('vendor.backpack.crud.fields.upload', [
                        'field' => [
                            'label' => __('Upload xls/csv file'),
                            'name' => 'file',
                            'upload' => true,
                            'hint'       => __('Please upload xls or csv file with following structure: <ul><li>1st column – email</li><li>2nd column - phone</li><li>3rd column - name</li><li>4th column - balance</li></ul>'),
                        ]
                    ])

                    @include('vendor.backpack.crud.fields.textarea', [
                        'field' => [
                            'label' => __('Users'),
                            'name' => 'data',
                            'attributes' => ['placeholder' => __('Email \t Phone \t Name \t Balance'), 'rows' => 7],
                        ]
                    ])


                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-success js-bulk-preview-button">
                        <span class="fa fa-eye" role="presentation" aria-hidden="true"></span> {{  __('Preview') }}
                    </button>
                </div>
            </div>
            {!! Form::close() !!}

            <div class="box js-bulk-preview">
            </div>
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

    <script>
        $(document).on('click','.js-bulk-preview-button', showPreviewTable);
        $(document).on('click', '.js-bulk-import-button', importBulk);

        function showPreviewTable() {
            var formData = new FormData();

            formData.append("title", $('input[name=title]').val());
            formData.append("mode", $('select[name=mode]').val());
            formData.append("file", $('input[name=file]').prop('files').length ? $('input[name=file]').prop('files')[0]: '');
            formData.append("data", $('textarea[name=data]').val());

            $.ajax({
                url: "{{ route('admin.user.previewBulk') }}",
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(table){
                    $('.js-bulk-preview').show();
                    $('.js-bulk-preview').html(table);
                }
            });

            return false;
        }
        
        function importBulk() {
            $('.js-bulk-form').submit();
        }
    </script>
@endsection

