<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Http\Requests\Admin\PartnerDepositRequest;
use App\Models\PartnerDeposit;
use App\Services\PartnerDepositService;
use App\Services\PartnerService;
use App\Traits\PartnerField;
use App\Traits\Search;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class PartnerDepositCrudController extends CrudController
{
    use Search;
    use PartnerField;

    private $searchableColumns = [
        'partners.title',
    ];

    /**
     * @var PartnerDepositService
     */
    protected $partnerDepositService;

    /**
     * @var PartnerService
     */
    protected $partnerService;

    public function __construct(PartnerDepositService $partnerDepositService, PartnerService $partnerService)
    {
        parent::__construct();

        $this->partnerDepositService = $partnerDepositService;
        $this->partnerService = $partnerService;
    }

    public function setup()
    {
        $this->crud->setModel(PartnerDeposit::class);
        $this->crud->setEntityNameStrings(__('Deposit'), __('Deposits'));
        $this->crud->setRoute('admin/partnerDeposit');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->allowAccess('confirm');
        $this->crud->allowAccess('reject');

        $this
            ->getCrudQuery()
            ->select(
                'partner_deposits.*',
                'partners.title'
            )
            ->join('partners', 'partners.id', '=', 'partner_deposits.partner_id');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'created_at',
                'label' => __('Bill date'),
            ],
            [
                'name' => 'partners.title',
                'label' => __('Merchant'),
                'type' => 'callback',
                'callback' => function (PartnerDeposit $row) {
                    return $row->partner->title;
                },
            ],
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'type' => 'callback',
                'callback' => function (PartnerDeposit $row) {
                    return \HAmount::fMedium($row->amount, $row->currency);
                },
            ],
            [
                'name' => 'fee',
                'label' => __('Fee'),
                'type' => 'callback',
                'callback' => function (PartnerDeposit $row) {
                    return \HAmount::fMedium($row->fee, $row->currency);
                },
            ],
            [
                'name' => 'confirmed_at',
                'label' => __('Paid date'),
            ],
            [
                'name' => 'status',
                'label' => __('Status'),
                'type' => 'callback',
                'callback' => function (PartnerDeposit $partnerDeposit) {
                    return $this->partnerDepositService->getStatus($partnerDeposit);
                },
            ],
        ]);

        $this->crud->addButton('line', 'confirm', 'view', 'crud.buttons.confirm', 'end');
        $this->crud->addButton('line', 'reject', 'view', 'crud.buttons.reject', 'end');

        $this->addPartnerField();

        $this->crud->addField([
            'name' => 'amount',
            'label' => __('Amount'),
        ]);

        $this->crud->addField([
            'name' => 'fee',
            'label' => __('Fee'),
            'value' => 6,
        ]);

        $this->crud->addField([
            'name' => 'fee_type',
            'label' => __('Fee type'),
            'type' => 'select_from_array',
            'options' => [
                'percent' => __('Percent'),
                'fixed' => __('Fixed'),
            ],
            'value' => 'percent',
        ]);
    }

    public function store(PartnerDepositRequest $request)
    {
        $request->request->set('status', PartnerDeposit::STATUS_PENDING);
        $feeType = $request->get('fee_type');

        if (PartnerDepositRequest::FEE_TYPE_PERCENT === $feeType) {
            $amount = $request->get('amount');
            $fee = round($amount / 100 * $request->get('fee'), 2);
            $request->request->set('fee', $fee);
            $request->request->set('amount', $amount - $fee);
        }

        return parent::storeCrud($request);
    }

    public function confirm($id)
    {
        try {
            \DB::transaction(function () use ($id) {
                /** @var PartnerDeposit $deposit */
                $partnerDeposit = PartnerDeposit::findOrFail($id);
                $partnerDeposit->status = PartnerDeposit::STATUS_CONFIRMED;
                $partnerDeposit->confirmed_at = Carbon::now();
                $partnerDeposit->saveOrFail();

                $this->partnerService->updateBalance($partnerDeposit->partner);

                \Alert::success(__('Deposit confirmed!'))->flash();
            });
        } catch (\Throwable $e) {
            \Alert::error(__('Unable to confirm deposit'))->flash();
        }

        return redirect()->back();
    }

    public function reject($id): RedirectResponse
    {
        try {
            \DB::transaction(function () use ($id) {
                /** @var PartnerDeposit $partnerDeposit */
                $partnerDeposit = PartnerDeposit::findOrFail($id);
                $partnerDeposit->status = PartnerDeposit::STATUS_REJECTED;
                $partnerDeposit->updated_at = Carbon::now();
                $partnerDeposit->saveOrFail();

                $this->partnerService->updateBalance($partnerDeposit->partner);

                \Alert::success(__('Deposit declined!'))->flash();
            });
        } catch (\Throwable $e) {
            \Alert::warning(__('Unable to decline deposit'))->flash();
        }

        return redirect()->back();
    }
}
