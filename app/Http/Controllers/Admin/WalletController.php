<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WalletExchangeRequest;
use App\Http\Requests\Admin\WalletRequest;
use App\Http\Requests\Admin\WalletSettingsRequest;
use App\Http\Requests\Admin\WalletTransactionsRequest;
use App\Http\Requests\Admin\WalletWithdrawRequest;
use App\Models\Partner;
use App\Models\WalletTransaction;
use App\Services\Treasury\ApiClient as TreasuryClient;
use App\Services\Treasury\TreasuryException;
use App\Services\Treasury\TreasuryLowBalanceException;
use App\Models\User;
use App\Crud\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\SaveActions;
use Backpack\CRUD\CrudPanel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Validator;

class WalletController extends CrudController
{
    use SaveActions;
    use ValidatesRequests;

    public $request;

    /* @var TreasuryClient $_treasuryService */
    protected $_treasuryService;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->request = $request;

            $partner = \Auth::user()->partner;
            $this->_treasuryService = app(TreasuryClient::class, ['userId' => $partner->id, 'apiKey' => $partner->mainAdministrator->api_token]);

            return $next($request);
        });

        parent::__construct();
    }

    public function main(WalletRequest $request)
    {
        // createWallet if we don't have one

        $partner = \Auth::user()->partner;

        $walletSuccess = false;

        try {
            if (!$partner->eth_address) {
                // we should create one
                $response = $this->_treasuryService->createWallet();

                $partner->eth_address = $response['address'];
                $partner->withdraw_key = $response['withdraw_key'];

                if (!$partner->save()) {
                    throw new TreasuryException('Failed to create wallet');
                }

                \Alert::success(__('Wallet successfully created'))->flash();
            }

            $walletSuccess = true;
        } catch (TreasuryException $e) {
            // log treasury error here
            \Log::error($e);

            // show message to user
            \Alert::error(__('Failed to create a purse'))->flash();
        }

        return view('wallet.index', ['walletSuccess' => $walletSuccess, 'partner' => $partner]);
    }

    protected function getTransactionCrud()
    {
        $crud = app()->make(CrudPanel::class);
        $crud->request = $this->request;

        $crud->setModel("App\Models\WalletTransaction");
        $crud->setRoute('admin/wallet');
        $crud->setEntityNameStrings(__('transaction'), __('transactions'));
        $crud->removeAllButtons();
        $crud->ajax_table = false;

        $blockchainViewer = config('treasury.viewer');

        $partner = \Auth::user()->partner;

        $crud->setColumns([
            [
                'name' => 'hash',
                'label' => __('Transaction hash'),
                'type' => 'tx',
                'viewer' => $blockchainViewer,
            ], [
                'label' => __('Sender'),
                'name' => 'from',
                'type' => 'eth_address',
                'viewer' => $blockchainViewer,
            ],
            [
                'name' => 'direction',
                'label' => __('Direction'),
                'type' => 'callback',
                'callback' => function ($item) use ($partner) {
                    return $partner->eth_address === $item->from ? ('<span class="label label-danger">'.__('OUT').'</span>') : ('<span class="label label-success">'.__('IN').'</span>');
                },
            ],
            [
                'name' => 'to',
                'label' => __('Recipient'),
                'type' => 'eth_address',
                'viewer' => $blockchainViewer,
            ],
            [
                'name' => 'amount_with_name',
                'label' => __('Amount'),
            ],
            [
                'name' => 'created_at',
                'label' => __('Date'),
                'type' => 'datetime',
            ],
            [
                'name' => 'status',
                'label' => __('Status'),
            ],
        ]);

        return $crud;
    }

    protected function getWithdrawCrud()
    {
        $crud = app()->make(CrudPanel::class);
        $crud->request = $this->request;

        $crud->setModel("App\Models\WalletWithdrawal");
        $crud->setRoute('admin/wallet/withdraw');
        $crud->allowAccess('create');
        $crud->removeAllButtons();

        $fields = collect([
            'address' => ['label' => __('Address')],
            'amount' => ['label' => __('Amount')],
            'currency' => [
                'label' => __('Currency'),
                'type' => 'select_from_array',
                'options' => \HCurrency::getAll(),
            ],
            'tx_fee' => [
                'label' => __('Transaction cost'),
                'attributes' => ['disabled' => 'disabled'],
            ],
        ]);

        $fields->each(function (array $field, string $name) use ($crud) {
            $crud->addField(array_merge(['name' => $name], $field));
        });

        return $crud;
    }

    /**
     * @return CrudPanel|mixed
     *
     * @throws TreasuryException
     */
    protected function getExchangeCrud()
    {
        $crud = app()->make(CrudPanel::class);
        $crud->request = $this->request;

        $crud->setModel("App\Models\WalletExchange");
        $crud->setRoute('admin/wallet/exchange');
        $crud->allowAccess('create');
        $crud->removeAllButtons();

        $partner = \Auth::user()->partner;

        $balance = $this->_treasuryService->getBalance();

        $fields = collect([
            'wallet' => [
                'label' => __('Choose sender wallet'),
                'type' => 'select_from_array',
                'options' => ['bitrewards' => __('BitRewards wallet').' - '.\HAmount::floor($balance['balanceEth']).' ETH', 'external' => __('External wallet')],
                'value' => 'bitrewards',
            ],
            'partnerAddress' => [
                'type' => 'hidden',
                'value' => $partner->eth_address,
            ],
            'address' => [
                'label' => __('Enter your Ethereum wallet address'),
                'wrapperAttributes' => [
                    'style' => 'display: none;',
                ],
                'value' => $partner->eth_address,
            ],
            'amount' => ['label' => __('Amount of ETH')],
        ]);

        $fields->each(function (array $field, string $name) use ($crud) {
            $crud->addField(array_merge(['name' => $name], $field));
        });

        return $crud;
    }

    /**
     * @param WalletTransactionsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws TreasuryException
     */
    public function listTransactions(WalletTransactionsRequest $request)
    {
        try {
            $transactions = array_map(function ($item) {
                return new WalletTransaction($item);
            }, $this->_treasuryService->listTransactions());
        } catch (TreasuryException $e) {
            \Log::error($e);

            return response()->json(['error' => __('Failed to get transaction list')]);
        }

        $crud = $this->getTransactionCrud();
        $data['crud'] = $crud;
        $data['title'] = ucfirst($crud->entity_name_plural);

        // get all entries if AJAX is not enabled
        $data['entries'] = $transactions;

        try {
            $view = view('wallet.transactions', $data)->render();
        } catch (\Throwable $e) {
            return response()->json(['error' => __('Failed to get transaction list')]);
        }

        return response()->json(['data' => $view]);
    }

    public function getBalance(WalletTransactionsRequest $request)
    {
        try {
            $balance = $this->_treasuryService->getBalance();
        } catch (TreasuryException $e) {
            \Log::error($e);

            return response()->json(['error' => __('Unable to get balance')]);
        }

        return response()->json(['balance' => $balance]);
    }

    public function getTokenTransferEthFeeEstimate()
    {
        try {
            $fee = $this->_treasuryService->getTokenTransferEthFeeEstimate();
        } catch (TreasuryException $e) {
            \Log::error($e);

            return response()->json(['error' => __('The transaction cost could not be estimated')]);
        }

        return response()->json(['fee' => $fee]);
    }

    public function getEthTransferFeeEstimate()
    {
        try {
            $fee = $this->_treasuryService->getEthTransferFeeEstimate();
        } catch (TreasuryException $e) {
            \Log::error($e);

            return response()->json(['error' => __('The transaction cost could not be estimated')]);
        }

        return response()->json(['fee' => $fee]);
    }

    protected function getSettingsCrud()
    {
        $crud = app()->make(CrudPanel::class);
        $crud->request = $this->request;

        $crud->setModel("App\Models\WalletSettings");
        $crud->setRoute(route('admin.wallet.settings'));
        $crud->allowAccess('create');
        $crud->removeAllButtons();

        $partner = \Auth::user()->partner;

        $fields = collect([
            'fee_type' => [
                'label' => __('Type of commission for withdrawal'),
                'type' => 'select_from_array',
                'options' => [Partner::BIT_WITHDRAWAL_FEE_TYPE_PERCENT => __('Percent'), Partner::BIT_WITHDRAWAL_FEE_TYPE_FIXED => __('Fixed')],
                'value' => $partner->getBitWithdrawFeeType(),
            ],
            'fee' => [
                'label' => __('Commission fee for withdrawal'),
                'value' => $partner->getBitWithdrawFee(),
            ],
            'min_withdrawal' => [
                'label' => __('Minimum withdrawal amount'),
                'value' => $partner->getBitWithdrawMinAmount(),
            ],
        ]);

        $fields->each(function (array $field, string $name) use ($crud) {
            $crud->addField(array_merge(['name' => $name], $field));
        });

        return $crud;
    }

    public function updateSettings(WalletSettingsRequest $request)
    {
        $data = $request->input();

        try {
            $partner = \Auth::user()->partner;

            \DB::transaction(function () use ($partner, $data) {
                $partner->setSetting(Partner::SETTINGS_BIT_WITHDRAWAL_FEE, $data['fee']);
                $partner->setSetting(Partner::SETTINGS_BIT_WITHDRAWAL_FEE_TYPE, $data['fee_type']);
                $partner->setSetting(Partner::SETTINGS_BIT_MIN_WITHDRAWAL, $data['min_withdrawal']);
                $partner->save();
            });

            \Alert::success(__('Settings updated'))->flash();

            return redirect()->route('admin.wallet.settings');
        } catch (\Throwable $e) {
            \Alert::error(__('Error saving settings'))->flash();
            \Log::error($e);
        }

        return redirect()->refresh()->withInput($request->input());
    }

    public function settingsForm(WalletRequest $request)
    {
        $crud = $this->getSettingsCrud();
        $data['crud'] = $crud;
        $data['saveAction'] = $this->getSaveAction();
        $data['fields'] = $crud->getCreateFields();

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('wallet.settings', $data);
    }

    public function withdrawForm(WalletTransactionsRequest $request)
    {
        $crud = $this->getWithdrawCrud();
        $data['crud'] = $crud;
        $data['saveAction'] = $this->getSaveAction();
        $data['fields'] = $crud->getCreateFields();

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('wallet.withdraw', $data);
    }

    public function withdrawRequest(WalletWithdrawRequest $request)
    {
        $data = $request->input();

        try {
            $partner = \Auth::user()->partner;
            $this->_treasuryService->withdraw(
                $data['address'],
                $data['amount'],
                $data['currency'],
                $partner->withdraw_key
            );

            \Alert::success(__('Transfer sent'))->flash();

            return redirect()->route('admin.wallet.index');
        } catch (TreasuryLowBalanceException $e) {
            \Alert::error(__('Not enough funds for transfer'))->flash();
        } catch (TreasuryException $e) {
            \Alert::error(__('Error while transferring funds'))->flash();
            \Log::error($e);
        }

        return redirect()->refresh()->withInput($request->input());
    }

    public function exchangeForm(WalletTransactionsRequest $request)
    {
        $crud = $this->getExchangeCrud();
        $data['crud'] = $crud;
        $data['saveAction'] = $this->getSaveAction();
        $data['fields'] = $crud->getCreateFields();

        return view('wallet.exchange', $data);
    }

    public function exchangeRequest(WalletExchangeRequest $request)
    {
        $data = $request->input();

        $exchangeAddress = config('treasury.exchange_address');

        $data['address'] = mb_strtolower(trim($data['address']));

        if ('bitrewards' === $data['wallet']) {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->refresh()->withErrors($validator)->withInput($request->input());
            }

            try {
                $partner = \Auth::user()->partner;
                $this->_treasuryService->withdraw(
                    $exchangeAddress,
                    $data['amount'],
                    \HCurrency::CURRENCY_ETH,
                    $partner->withdraw_key
                );

                \Alert::success(__('Transfer sent'))->flash();

                return redirect()->route('admin.wallet.index');
            } catch (TreasuryLowBalanceException $e) {
                \Alert::error(__('Not enough funds for transfer'))->flash();
            } catch (TreasuryException $e) {
                \Alert::error(__('Error while transferring funds'))->flash();
                \Log::error($e);
            }
        } else {
            $user = \Auth::user();
            // check that no partner or user with such wallet exists
            if (
                Partner::query()
                    ->where('eth_address', $data['address'])
                    ->join('administrators', 'administrators.partner_id', 'partners.id')
                    ->where('administrators.is_main', true)
                    ->where('administrators.id', '<>', $user->id)->count()
                ||
                User::model()
                    ->where('eth_sender_address', '=', $data['address'])
                    ->where('id', '<>', $user->id)->count()
            ) {
                \Alert::error(__('Wallet is already in use by another user'))->flash();

                return redirect()->refresh()->withInput($request->input());
            }

            $user->eth_sender_address = $data['address'];

            if ($user->save()) {
                return view('wallet.exchange_addresses', [
                    'from_address' => $data['address'],
                    'to_address' => $exchangeAddress,
                ]);
            } else {
                \Alert::error(__('Failed to save address'))->flash();

                return redirect()->refresh()->withInput($request->input());
            }
        }

        return redirect()->refresh()->withInput($request->input());
    }
}
