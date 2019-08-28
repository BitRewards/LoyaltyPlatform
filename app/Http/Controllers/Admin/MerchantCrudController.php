<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Models\Partner;
use App\Traits\Search;
use Illuminate\Database\Query\JoinClause;

class MerchantCrudController extends CrudController
{
    use Search;

    private $searchableColumns = [
        'title',
    ];

    public function setup()
    {
        $this->crud->setModel(Partner::class);
        $this->crud->addClause('whereRaw', "(partner_settings->>'is-fiat-referral-enabled')::boolean is true");
        $this->crud->setRoute('admin/merchant');
        $this->crud->setEntityNameStrings(__('merchant'), __('merchants'));
        $this->crud->removeAllButtons();

        $this->getCrudQuery()->select(
            'partners.id',
            'partners.title',
            \DB::raw('COALESCE(SUM(partner_deposits.amount), 0) as balance'),
            \DB::raw('MAX(partner_deposits.created_at) as last_deposit')
        );
        $this->getCrudQuery()->leftJoin('partner_deposits', function (JoinClause $joinClause) {
            $joinClause->on('partners.id', 'partner_deposits.partner_id');
            $joinClause->on('partner_deposits.status', '=', \DB::raw("'confirmed'"));
        });
        $this->getCrudQuery()->groupBy('partners.id');

        $this->crud->setColumns(['id', 'title', 'balance', 'last_deposit']);
    }
}
