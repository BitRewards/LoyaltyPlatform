<?php

namespace App\Http\Controllers\Admin;

use App\Administrator;
use App\Http\Requests\Admin\CashierUserRequest;
use App\Http\Requests\Admin\UpdateCashierUserRequest;
use App\Traits\CreatedAtFilters;
use App\Traits\EntityBinder;
use App\Traits\PartnerFilter;
use App\Traits\RelationFilter;
use App\Traits\Search;
use App\Crud\CrudController;
use Illuminate\Http\Request;

class CashierUsersController extends CrudController
{
    use Search, CreatedAtFilters, PartnerFilter, RelationFilter, EntityBinder;

    public function setup()
    {
        $this->crud->setModel(Administrator::class);
        $this->crud->setRoute('admin/cashier-users');
        $this->crud->enableAjaxTable();
        $this->crud->setEntityNameStrings(__('cashier'), __('cashiers'));
        $this->crud->allowAccess('edit');
        $this->crud->allowAccess('delete');
        $this->crud->allowAccess('update');
        $this->crud->setColumns([
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'name', 'label' => __('Name')],
            ['name' => 'email', 'label' => __('Email')],
            ['name' => 'created_at', 'label' => __('Created')],
            [
                'name' => 'auth_url',
                'label' => __('Authentication Link'),
                'type' => 'callback',
                'callback' => function (Administrator $user) {
                    $url = route('cashier.index', ['api_token' => $user->api_token]);

                    return '<a href="'.$url.'" target="_blank">'.__('Open').'</a>';
                },
            ],
        ]);

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();
        $this->crud->addClause('where', 'role', Administrator::ROLE_CASHIER);

        $fields = collect([
            'name' => ['label' => __('Name')],
            'email' => ['label' => __('Email')],
        ]);

        $fields->each(function (array $field, string $name) {
            $this->crud->addField(array_merge(['name' => $name], $field));
        });
    }

    /**
     * @param CashierUserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CashierUserRequest $request)
    {
        $result = parent::storeCrud($request);

        if (!is_null($this->crud->entry)) {
            $this->processCashierUser($request, $this->crud->entry);
        }

        return $result;
    }

    /**
     * @param UpdateCashierUserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCashierUserRequest $request)
    {
        return parent::updateCrud($request);
    }

    protected function processCashierUser(Request $request, Administrator $user)
    {
        $user->role = Administrator::ROLE_CASHIER;
        $user->partner_id = $request->user()->partner_id;
        $user->save();
    }
}
