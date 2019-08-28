@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>{{ __('Wallet') }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li class="active">{{ __('Wallet') }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            @if ($walletSuccess)
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-12">
                    <a href="{{ url('admin/wallet/exchange') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"> {{ __('Buy BIT') }} </span></a>
                    <a href="{{ url('admin/wallet/withdraw') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"> {{ __('Withdraw') }} </span></a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="balance-details">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Balance') }}</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body empty-wallet-details">
                            <i class="fa fa-spin fa-refresh"></i>
                        </div>
                        <div class="box-body wallet-balance-details" style="display: none;">



                            <p>
                                <span class="text-muted" id="wallet-balance-eth"></span>
                                <strong><i class="fa margin-r-5"></i> {{__('ETH')}}</strong>
                            </p>

                            <hr>

                            <p>
                                <span class="text-muted" id="wallet-balance-bit"></span>
                                <strong><i class="fa margin-r-5"></i> {{__('BIT')}}</strong>
                            </p>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('Deposit') }}</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <span>{{ __ ('Send BIT tokens and ETH to this address') }}</span>
                            <span class="text-muted"><?= $partner->eth_address?></span>
                            <div>
                            <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?= $partner->eth_address ?>&choe=UTF-8" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box">

                        <div class="box-header">
                            <h3 class="box-title"><span class="text-capitalize">{{ __('Recent transactions') }}</span></h3>
                            <div id="datatable_button_stack" class="pull-right text-right"></div>
                        </div>

                        <div class="box-body table-responsive" id="transactions-list">
                            <i class="fa fa-spin fa-refresh"></i>
                        </div><!-- /.box-body -->

                    </div><!-- /.box -->

                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@section('after_styles')
    <!-- DATA TABLES -->
    <link href="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')


@endsection

@section('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $.ajax({
                url: '{{ url('admin/wallet/transactions') }}',
                type: 'GET',
            }).done(function(data) {
                if (data.error) {
                    new PNotify({
                        title: "{{ __('Error') }}",
                        text: data.error,
                        type: "warning"
                    });
                    return;
                }
                $('#transactions-list').html(data.data);
            });

            $.ajax({
                url: '{{ url('admin/wallet/get-balance') }}',
                type: 'GET',
            }).done(function(data) {
                if (data.error) {
                    new PNotify({
                        title: "{{ __('Error') }}",
                        text: data.error,
                        type: "warning"
                    });
                    return;
                }
                // $('#wallet-address').html(data.balance.address);
                $('#wallet-balance-bit').html(data.balance.balanceBIT);
                $('#wallet-balance-eth').html(data.balance.balanceEth);

                $('.empty-wallet-details').hide();
                $('.wallet-balance-details').show();
            });
        })
    </script>
@endsection