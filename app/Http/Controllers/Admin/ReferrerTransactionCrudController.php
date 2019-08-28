<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Models\Action;
use App\Models\Reward;
use App\Models\Transaction;
use App\Traits\Search;
use App\Traits\TransactionActionsTrait;
use Illuminate\Database\Eloquent\Builder;

class ReferrerTransactionCrudController extends CrudController
{
    use Search, TransactionActionsTrait;

    private $searchableColumns = [
        'partners.title',
        'users.name',
        'users.email',
        'users.phone',
    ];

    public function setup()
    {
        $this->crud->setModel(Transaction::class);
        $this->crud->setRoute('admin/referrerTransaction');
        $this->crud->setEntityNameStrings(__('transaction'), __('transactions'));
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
            ->leftJoin('rewards', 'rewards.id', '=', 'transactions.reward_id')
            ->leftJoin('actions', 'actions.id', '=', 'transactions.action_id')
            ->where(function (Builder $builder) {
                $builder
                    ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW)
                    ->orWhere('actions.type', Action::TYPE_ORDER_REFERRAL);
            });

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
                'label' => __('User'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $row->user->name;
                },
            ],
            [
                'name' => 'users.email',
                'label' => __('Email'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return $row->user->email;
                },
            ],
            [
                'name' => 'users.phone',
                'label' => __('Phone number'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return $row->user->phone;
                },
            ],
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::pointsToFiatFormatted($row->balance_change, $row->partner);
                },
            ],
            [
                'name' => 'balance_before',
                'label' => __('Balance before'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::pointsToFiatFormatted($row->balance_before, $row->partner);
                },
            ],
            [
                'name' => 'balance_after',
                'label' => __('Balance after'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    return \HAmount::pointsToFiatFormatted($row->balance_after, $row->partner);
                },
            ],
            [
                'name' => 'merchant_balance_before',
                'label' => __('Merchant balance before'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    $balanceBefore = $row->getMerchantBalanceBefore();

                    if (null === $balanceBefore) {
                        return '';
                    }

                    return \HAmount::fMedium($balanceBefore, $row->partner->currency);
                },
            ],
            [
                'name' => 'merchant_balance_after',
                'label' => __('Merchant balance after'),
                'type' => 'callback',
                'callback' => function (Transaction $row) {
                    $balanceAfter = $row->getMerchantBalanceAfter();

                    if (null === $balanceAfter) {
                        return '';
                    }

                    return \HAmount::fMedium($balanceAfter, $row->partner->currency);
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
