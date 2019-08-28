<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Http\Requests\Admin\MassAwardRequest;
use App\Http\Requests\Admin\RewardRequest as StoreRequest;
use App\Http\Requests\Admin\RewardRequest as UpdateRequest;
use App\Jobs\GiveMassAward;
use App\Models\Partner;
use App\Models\User;
use App\Traits\Search;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Traits\EntityBinder;
use App\Traits\EntityCopy;
use App\Traits\PartnerField;
use App\Models\Reward;

class RewardCrudController extends CrudController
{
    use Search, RelationFilter, CreatedAtFilters, PartnerFilter, EntityBinder, EntityCopy, PartnerField;

    private $searchableColumns = [
        'price',
        'price_type',
    ];

    public function setUp()
    {
        $this->crud->setModel("App\Models\Reward");
        $this->crud->setRoute('admin/reward');
        $this->crud->setEntityNameStrings(__('reward'), __('rewards'));
        $this->crud->addButton('line', 'copy', 'view', 'crud.buttons.copy', 'end');
        $this->crud->addButton('top', 'mass_award', 'add', 'crud.buttons.mass_award', 'end');
        $this->crud->enableAjaxTable();
        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ], [
                'label' => __('Title'),
                'name' => 'title',
                'type' => 'callback',
                'callback' => function (Reward $reward) {
                    return \HReward::getTitle($reward);
                },
            ], [
                'name' => 'price',
                'label' => __('Price'),
                'type' => 'callback',
                'callback' => function (Reward $reward) {
                    return \HReward::getPriceStr($reward);
                },
            ], [
                'name' => 'price_type',
                'label' => __('Price Type'),
                'type' => 'callback',
                'callback' => function (Reward $reward) {
                    return \HReward::getPriceTypeStr($reward);
                },
            ], [
                'name' => 'created_at',
                'label' => __('Date'),
            ], [
                'label' => __('Status'),
                'name' => 'status',
                'type' => 'callback',
                'callback' => function (Reward $reward) {
                    return \HReward::getStatusStr($reward);
                },
            ], [
                'label' => __('Tag'),
                'name' => 'tag',
            ],
        ]);

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();

        $this->crud->addField([
            'name' => 'title',
            'label' => __('Title'),
            'type' => 'callback',
            'callback' => function ($id) {
                return \HReward::getTitle(Reward::find($id));
            },
        ]);

        $this->crud->addField([
            'name' => 'price',
            'label' => __('Amount'),
            'type' => 'number',
        ]);

        $this->crud->addField([
            'name' => 'price_type',
            'label' => __('Price Type'),
            'type' => 'select_from_array',
            'options' => \HReward::getAllPriceTypes(),
        ]);

        $this->crud->addField([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => \HReward::getAll(),
        ]);

        $this->crud->addField([
            'name' => 'value',
            'label' => __('Value'),
            'type' => 'number',
        ]);

        $this->crud->addField([
            'name' => 'value_type',
            'label' => __('Value type'),
            'type' => 'select_from_array',
            'options' => \HReward::getAllValueTypes(),
        ]);

        $this->crud->addField([
            'name' => 'status',
            'label' => __('Status'),
            'type' => 'select_from_array',
            'options' => \HReward::getAllStatuses(),
        ]);

        $this->crud->addField([
            'name' => 'tag',
            'label' => __('Tag'),
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => __('Description'),
            'type' => 'textarea',
        ]);

        $this->crud->addField([
            'name' => 'description_short',
            'label' => __('Description (short)'),
            'placeholder' => __('Displays in reward list with grey font'),
            'type' => 'textarea',
        ]);

        if (\Auth::user()->can('admin')) {
            $this->crud->addField([
                'name' => 'config',
                'label' => __('Configuration'),
                'type' => 'json',
            ]);
        }

        $this->addPartnerField();
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud();
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud();
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function edit($id)
    {
        if (is_scalar($id)) {
            $entry = $this->crud->getEntry($id);
        } else {
            $entry = $id;
        }

        /*
         * @var \App\Models\Reward $entry
         */
        \HLanguage::setLanguage($entry->partner->default_language);
        $result = parent::edit($entry->id);
        \HLanguage::restorePreviousLanguage();

        return $result;
    }

    public function createMassAward()
    {
        return view('rewards/mass');
    }

    public function storeMassAward(MassAwardRequest $request)
    {
        dispatch(new GiveMassAward($request->partner_id, $request->points, $request->comment, $request->onlyConfirmedEmails));

        $partner = Partner::whereId($request->partner_id)->first();
        $message = __('%points% points has been given to all users for %partnerTitle%', ['points' => $request->points, 'partnerTitle' => $partner->title]);
        \Alert::success($message)->flash();

        return redirect(route('admin.rewards.createMassAward'));
    }
}
