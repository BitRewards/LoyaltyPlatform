<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Models\Reward;
use App\Models\Transaction;
use App\Traits\Search;
use App\Traits\TransactionActionsTrait;

class ReferrerWithdrawCrudController extends CrudController
{
    use Search, TransactionActionsTrait;

    private $searchableColumns = [
        'partners.title',
        'users.name',
    ];

    public function setup()
    {
        $this->crud->setModel(Transaction::class);
        $this->crud->setRoute('admin/referrerWithdraw');
        $this->crud->setEntityNameStrings(__('payment'), __('payments'));
        $this->crud->removeAllButtons();
        $this->crud->addButton('line', 'confirm', 'view', 'crud.buttons.confirm', 'end');
        $this->crud->addButton('line', 'reject', 'view', 'crud.buttons.reject', 'end');
        $this->crud->allowAccess('confirm');
        $this->crud->allowAccess('reject');

        $this
            ->getCrudQuery()
            ->select(
                'transactions.*',
                'users.name',
                'partners.title'
            )
            ->join('partners', 'partners.id', '=', 'transactions.partner_id')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->join('rewards', 'rewards.id', '=', 'transactions.reward_id')
            ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW)
        ;

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'created_at',
                'label' => __('Created'),
            ],
            [
                'name' => 'partners.title',
                'label' => __('Merchant'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $row->partner->title;
                },
            ],
            [
                'name' => 'users.name',
                'label' => __('Referrer'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $row->user->name;
                },
            ],
            [
                'name' => 'fiat_withdraw_first_name',
                'label' => __('Recipient name'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return $row->getFiatWithdrawFirstName();
                },
            ],
            [
                'name' => 'fiat_withdraw_second_name',
                'label' => __('Recipient second name'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return $row->getFiatWithdrawLastName();
                },
            ],
            [
                'name' => 'fiat_withdraw_card_number',
                'label' => __('Recipient card number'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return $row->getFiatWithdrawCardNumber();
                },
            ],
            [
                'name' => 'fiat_withdraw_amount',
                'label' => __('Amount'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::fMedium(abs($row->balance_change), $row->partner->currency);
                },
            ],
            [
                'name' => 'fiat_withdraw_fee',
                'label' => __('Fee'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::fMedium($row->getFiatWithdrawFee(), $row->partner->currency);
                },
            ],
            [
                'name' => 'fiat_withdraw_sent',
                'label' => __('Sent'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::fMedium($row->getFiatWithdrawAmount(), $row->partner->currency);
                },
            ],
            [
                'name' => 'confirmed_at',
                'label' => __('Confirmed'),
            ],
            [
                'name' => 'status',
                'label' => __('Status'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HTransaction::getStatusStr($row);
                },
            ],
        ]);
    }
}
