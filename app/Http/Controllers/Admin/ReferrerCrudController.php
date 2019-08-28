<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\User;
use App\Traits\AggregateCounterTrait;
use App\Traits\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class ReferrerCrudController extends CrudController
{
    use AggregateCounterTrait, Search;

    private $searchableColumns = [
        'partners.title',
    ];

    protected function toFiat($points, Partner $partner): string
    {
        $fiatAmount = \HAmount::pointsToFiat($points, $partner);

        return \HAmount::fMedium($fiatAmount, $partner->currency);
    }

    public function setup()
    {
        $this->crud->setModel(User::class);
        $this->crud->setRoute('admin/referrer');
        $this->crud->setRowsCounter([$this, 'rowCounter']);
        $this->crud->setEntityNameStrings(__('referrer'), __('referrers'));
        $this->crud->removeAllButtons();

        $this
            ->getCrudQuery()
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.partner_id',
                'partners.title',
                \DB::raw("COALESCE(SUM(CASE WHEN transactions.status = 'confirmed' AND actions.type = 'OrderReferral' THEN transactions.balance_change ELSE 0 END), 0) as earned"),
                \DB::raw("COALESCE(SUM(CASE WHEN transactions.status = 'confirmed' AND rewards.type = 'FiatWithdraw' THEN transactions.balance_change ELSE 0 END), 0) as blocked"),
                \DB::raw("COALESCE(SUM(CASE WHEN transactions.status = 'pending' AND rewards.type = 'FiatWithdraw' THEN transactions.balance_change ELSE 0 END), 0) * -1 as paid")
            )
            ->join('partners', function (JoinClause $join) {
                $join
                    ->on('partners.id', '=', 'users.partner_id')
                    ->whereRaw("(partners.partner_settings->>'is-fiat-referral-enabled')::boolean IS TRUE");
            })
            ->join('transactions', 'transactions.user_id', '=', 'users.id')
            ->leftJoin('actions', 'actions.id', '=', 'transactions.action_id')
            ->leftJoin('rewards', 'rewards.id', '=', 'transactions.reward_id')
            ->where(function (Builder $query) {
                $query
                    ->where('actions.type', Action::TYPE_ORDER_REFERRAL)
                    ->orWhere('rewards.type', Reward::TYPE_FIAT_WITHDRAW);
            })
            ->groupBy('users.id', 'partners.id')
        ;

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'name',
                'label' => __('Referrer name'),
            ],
            [
                'name' => 'email',
                'label' => __('Email'),
            ],
            [
                'name' => 'phone',
                'label' => __('Phone number'),
            ],
            [
                'name' => 'partners.title',
                'label' => __('Partner'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $row->title;
                },
            ],
            [
                'name' => 'balance',
                'label' => __('Balance'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $this->toFiat($row->earned - $row->blocked - $row->paid, $row->partner);
                },
            ],
            [
                'name' => 'blocked',
                'label' => __('On payment'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $this->toFiat($row->blocked, $row->partner);
                },
            ],
            [
                'name' => 'paid',
                'label' => __('Paid'),
                'type' => 'callback',
                'callback' => function ($row) {
                    return $this->toFiat($row->paid, $row->partner);
                },
            ],
        ]);
    }
}
