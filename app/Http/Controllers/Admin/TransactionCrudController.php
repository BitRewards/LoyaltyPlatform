<?php

namespace App\Http\Controllers\Admin;

use App\Administrator;
use App\Excel\TransactionReport;
use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\User;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\TransactionRequest as StoreRequest;
use App\Http\Requests\Admin\TransactionRequest as UpdateRequest;
use App\Models\Transaction;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Traits\TransactionActionsTrait;
use Carbon\Carbon;

class TransactionCrudController extends \App\Crud\CrudController
{
    // FIXME pull request to backpack
    use RelationFilter, CreatedAtFilters, PartnerFilter, TransactionActionsTrait;

    private $orderBy = [
        'id' => 'desc',
    ];

    private $with = [
        'action',
        'reward',
        'actor',
        'sourceStoreEntity',
    ];

    public function setUp()
    {
        $this->crud->setModel(Transaction::class);
        $this->crud->setRoute('admin/transaction');
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->allowAccess('confirm');
        $this->crud->allowAccess('reject');
        $this->crud->allowAccess('export');
        $this->crud->addButton('top', 'export', 'view', 'crud.buttons.export', 'end');
        $this->crud->addButton('line', 'confirm', 'view', 'crud.buttons.confirm', 'end');
        $this->crud->addButton('line', 'reject', 'view', 'crud.buttons.reject', 'end');
        $this->crud->enableAjaxTable();
        $this->crud->setEntityNameStrings(__('transaction'), __('transactions'));
        $this->crud->addClause('with', 'user');
        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'user_id',
                'label' => __('Name'),
                'type' => 'select',
                'entity' => 'user',
                'attribute' => 'name',
                'model' => User::class,
            ],
            [
                'label' => __('Phone'),
                'name' => 'user_id',
                'type' => 'select',
                'entity' => 'user',
                'attribute' => 'phone',
                'key' => 'phone',
                'model' => User::class,
            ],
            [
                'label' => __('Email'),
                'name' => 'user_id',
                'type' => 'select',
                'entity' => 'user',
                'attribute' => 'email',
                'key' => 'email',
                'model' => User::class,
            ],
            [
                'name' => 'balance_change',
                'label' => __('Balance change'),
            ],
            [
                'name' => 'balance_before',
                'label' => __('Balance before'),
                'searchLogic' => false,
            ],
            [
                'name' => 'balance_after',
                'label' => __('Balance after'),
                'searchLogic' => false,
            ],
            [
                'label' => __('Status'),
                'type' => 'callback',
                'name' => 'status',
                'callback' => function (Transaction $transaction) {
                    return \HTransaction::getStatusStr($transaction);
                },
            ],
            [
                'name' => 'created_at',
                'label' => __('Date'),
            ],
            [
                'name' => 'confirmed_at',
                'label' => __('Confirmed'),
            ],
            [
                'label' => __('Action'),
                'name' => 'action_id',
                'type' => 'callback',
                'callback' => function (Transaction $transaction) {
                    return isset($transaction->action) ? \HAction::getTitle($transaction->action) : '';
                },
            ],
            [
                'label' => __('Reward'),
                'name' => 'reward_id',
                'type' => 'callback',
                'callback' => function (Transaction $transaction) {
                    return isset($transaction->reward) ? \HReward::getTitle($transaction->reward) : '';
                },
            ],
            [
                'label' => __('Cashier'),
                'name' => 'actor_id',
                'type' => 'callback',
                'callback' => function (Transaction $transaction) {
                    return !is_null($transaction->actor) ? $transaction->actor->name : '-';
                },
            ],
            [
                'label' => __('Social Network Post'),
                'name' => 'social_network_post',
                'type' => 'callback',
                'callback' => function (Transaction $transaction) {
                    $chunks = [];

                    if (isset($transaction->sourceStoreEntity->data->url)) {
                        $chunks[] = '<a href="'.$transaction->sourceStoreEntity->data->url.'" target="_blank">'.__('Post URL').'</a>';
                    }

                    if (isset($transaction->sourceStoreEntity->data->image_url)) {
                        $chunks[] = '<a href="'.$transaction->sourceStoreEntity->data->image_url.'" target="_blank">'.__('Post Image').'</a>';
                    }

                    return implode('<br>', $chunks);
                },
            ],
        ]);

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();

        $this->crud->addFilter(
            [
                'name' => 'action',
                'type' => 'select2',
                'label' => '<i class="fa fa-hand-pointer-o"></i> '.__('Action'),
            ],
            \HAction::getAll(),
            function ($type) {
                // FIXME due to backpack logic now is impossible to use join
                $actions = Action::model()->where('type', $type)->get();
                $ids = $actions->pluck('id')->toArray();
                $this->crud->addClause('whereIn', 'action_id', $ids);
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'reward',
                'type' => 'select2',
                'label' => '<i class="fa fa-rub"></i> '.__('Reward'),
            ],
            \HReward::getAll(),
            function ($type) {
                // FIXME due to backpack logic now is impossible to use join
                $rewards = Reward::model()->where('type', $type)->get();
                $ids = $rewards->pluck('id')->toArray();
                $this->crud->addClause('whereIn', 'reward_id', $ids);
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'actor',
                'type' => 'select2',
                'label' => '<i class="fa fa-user"></i> '.__('Cashier'),
            ],
            $this->cashierUsers(\Auth::user()),
            function (int $actorId) {
                $this->crud->addClause('where', 'actor_id', $actorId);
            }
        );

        if (!\Auth::user()->can('admin')) {
            $this->orderBy = ['id' => 'asc'];
        }
    }

    /**
     * Get cashier users of given User.
     *
     * @param User $user
     *
     * @return array
     */
    protected function cashierUsers(Administrator $user)
    {
        return Administrator::where('partner_id', $user->partner_id)
            ->where('role', Administrator::ROLE_CASHIER)
            ->get()
            ->keyBy('id')
            ->map(function (User $user) {
                return $user->name;
            })
            ->toArray();
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }

    public function export()
    {
        /** @var Partner $partner */
        $partner = \Auth::user()->partner;

        if (!$partner) {
            return abort(403);
        }

        return \Excel::download(new TransactionReport(
            $partner,
            Carbon::now()->subMonth(3),
            Carbon::now()
        ), 'transactions.xlsx');
    }

    public function redirectToUser($id)
    {
        $transaction = Transaction::whereId($id)->first();

        if (!$transaction) {
            abort(404);
        }

        if (\Auth::user()->partner_id && \Auth::user()->partner_id != $transaction->partner_id) {
            abort(403);
        }

        return redirect('/admin/user/'.$transaction->user_id);
    }
}
