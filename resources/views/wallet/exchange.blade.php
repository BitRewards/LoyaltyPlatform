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

@push('crud_fields_scripts')
    <script>
		jQuery(document).ready(function() {

			const partnerAddressInput = $('[name="partnerAddress"]').get(0);
			const partnerAddress = partnerAddressInput.value;

			const amountInput = $('[name="amount"]').get(0);
			const addressInput = $('[name="address"]').get(0);
			const exchangeRate = $('#js-exchange-rate').html();
			const ethereumNotice = $('.js-ethereum-wallet-notice').get(0);

            let select = $('[name="wallet"]');

			const walletChange = function () {
				const el = select;
				const wallet = el.length ? el.get(0).value : 0;

				if (wallet === 'bitrewards') {
					addressInput.value = partnerAddress
                    $(addressInput).parent().hide();
					$(amountInput).parent().show();
					$(ethereumNotice).hide();
                } else {
					addressInput.value = '';
					$(addressInput).parent().show();
					$(amountInput).parent().hide();
					$(ethereumNotice).show();
                }
			}

			const noticeUpdate = function () {
				if (parseFloat(amountInput.value) > 0) {
					$('#js-exchange-amount').html(parseFloat(parseFloat(amountInput.value) * exchangeRate).toFixed(2));
					$('#js-exchange-notice').show();
                } else {
					$('#js-exchange-notice').hide();
                }
            }


			select.change(walletChange);
			$(amountInput).keyup(noticeUpdate);
			jQuery(document).ready(walletChange);
		})
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
            @include('crud::inc.grouped_errors')

            {!! Form::open(array('url' => $crud->route, 'method' => 'post')) !!}
            <div class="box">

                <div class="box-body row">
                    <div class="form-group col-md-12">
                        <p>Here you can buy BIT for ETH.</p>
                        <p>Choose the ETH wallet and follow instructions.</p>
                    </div>
                    <!-- load the view from the application if it exists, otherwise load the one in the package -->
                    @if(view()->exists('vendor.backpack.crud.form_content'))
                        @include('vendor.backpack.crud.form_content', ['fields' => $crud->getFields('create'), 'action' => 'create'])
                    @else
                        @include('crud::form_content', ['fields' => $crud->getFields('create'), 'action' => 'create'])
                    @endif

                    <div class="form-group col-md-12 js-ethereum-wallet-notice">
                        <p>DO:<br/>Your personal Ethereum wallet address: MetaMask, Mist, MyEtherWallet, Parity, Trust, imToken etc</p>
                        <p class="text-danger">DO NOT:<br />Exchange addresses, other service's addresses, other user's addresses.</p>
                    </div>

                    <div class="form-group col-md-12">
                        <p>Current exchange rate:<br />
                            <span class="">1 BIT = <?= number_format(app(\App\Services\Fiat\FiatService::class)->getExchangeRate('BIT', 'ETH'), 5, '.', '') ?> ETH</span>
                            <br />
                            <span class="">1 ETH = <span id="js-exchange-rate"><?= number_format(app(\App\Services\Fiat\FiatService::class)->getExchangeRate('ETH', 'BIT'), 0, '.', '') ?></span> BIT</span>
                        </p>
                        <p id="js-exchange-notice" style="display: none;">You will get about <span id="js-exchange-amount"></span> BIT. The exchange rate is approximate.</p>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">

                    <div id="saveActions" class="form-group">

                        <input type="hidden" name="save_action" value="{{ $saveAction['active']['value'] }}">

                        <div class="btn-group">

                            <button type="submit" class="btn btn-success">
                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                <span>{{ __('Buy BIT for ETH') }}</span>
                            </button>

                        </div>

                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
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


