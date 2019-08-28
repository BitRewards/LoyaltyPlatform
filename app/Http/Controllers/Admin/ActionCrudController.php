<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Http\Requests\Admin\CreateActionRequest;
use App\Http\Requests\Admin\UpdateActionRequest;
use App\Models\Action;
use App\Traits\EntityBinder;
use App\Traits\ExtraFields;
use App\Traits\Search;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Traits\EntityCopy;
use App\Traits\PartnerField;

class ActionCrudController extends CrudController
{
    use Search, RelationFilter, CreatedAtFilters, PartnerFilter, EntityBinder, EntityCopy, PartnerField, ExtraFields;

    private $searchableColumns = [
        'title',
        'value',
    ];

    public function setUp()
    {
        $this->crud->setModel('App\Models\Action');
        $this->crud->setRoute('admin/action');
        $this->crud->enableAjaxTable();
        $this->crud->setEntityNameStrings(__('action'), __('actions'));
        $this->crud->addButton('line', 'copy', 'view', 'crud.buttons.copy', 'end');
        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ], [
                'name' => 'title',
                'label' => __('Title'),
                'type' => 'callback',
                'callback' => function (Action $action) {
                    return \HAction::getTitle($action);
                },
            ], [
                'name' => 'value',
                'label' => __('Reward'),
                'type' => 'callback',
                'callback' => function (Action $action) {
                    return \HAction::getRewardStr($action);
                },
            ], [
                'name' => 'created_at',
                'label' => __('Date'),
            ], [
                'label' => __('Status'),
                'name' => 'status',
                'type' => 'callback',
                'callback' => function (Action $action) {
                    return \HAction::getStatusStr($action);
                },
            ], [
                'label' => __('Tag'),
                'name' => 'tag',
            ],
        ]);

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();

        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'select2',
            'label' => '<i class="fa fa-check-circle"></i> '.__('Status'),
        ],
        \HAction::getAllStatuses(),
        function ($status) {
            $this->crud->addClause('where', 'status', $status);
        });

        $this->crud->addField([
            'name' => 'title',
            'label' => 'Название',
            'type' => 'callback',
            'callback' => function ($id) {
                return \HAction::getTitle(Action::find($id));
            },
        ]);

        $this->crud->addField([
            'name' => 'type',
            'label' => 'Тип',
            'type' => 'select_from_array',
            'options' => \HAction::getAll(),
        ]);

        $this->crud->addField([
            'name' => 'value',
            'label' => __('Value'),
            'type' => 'number',
            'attributes' => [
                'step' => 'any',
            ],
        ]);

        $this->crud->addField([
            'name' => 'value_type',
            'label' => __('Value type'),
            'type' => 'select_from_array',
            'options' => \HAction::getAllValueTypes(),
        ]);

        $this->crud->addField([
            'name' => 'status',
            'label' => __('Status'),
            'type' => 'select_from_array',
            'options' => \HAction::getAllStatuses(),
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
            'name' => 'limit_per_user',
            'label' => __('Limit per user').' <small style="color:#999">'.__('How many times one user can receive this reward?').'</small>',
            'type' => 'number',
        ]);

        $this->addExtraFields('config', [
            Action::CONFIG_REFERRAL_REWARD_VALUE => [
                'label' => __('Referral Reward Value'),
                'type' => 'number',
                'attributes' => [
                    'step' => 'any',
                ],
            ],
            Action::CONFIG_REFERRAL_REWARD_VALUE_TYPE => [
                'label' => __('Referral Reward Value type'),
                'type' => 'select_from_array',
                'options' => \HAction::getAllValueTypes(),
            ],
            Action::CONFIG_REFERRAL_REWARD_MIN_AMOUNT_TOTAL => [
                'label' => __('Referral Reward Min Amount Total'),
                'type' => 'number',
            ],
        ]);

        if (\Auth::user()->can('admin')) {
            $this->crud->addField([
                'name' => 'config',
                'label' => __('Configuration'),
                'type' => 'json',
            ]);
        }

        $this->addPartnerField();

        $this->crud->addClause('where', 'is_system', false);
    }

    public function store(CreateActionRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud();
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateActionRequest $request)
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
         * @var \App\Models\Action $entry
         */
        \HLanguage::setLanguage($entry->partner->default_language);
        $result = parent::edit($entry->id);
        \HLanguage::restorePreviousLanguage();

        return $result;
    }
}
