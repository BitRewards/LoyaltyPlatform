<?php

namespace App\Http\Controllers\Admin;

use App\Models\SpecialOfferReward;
use App\Crud\CrudController;
use App\Http\Requests\Admin\SpecialOfferRewardSaveRequest as StoreRequest;
use App\Http\Requests\Admin\SpecialOfferRewardSaveRequest as UpdateRequest;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Traits\EntityBinder;
use App\Traits\EntityCopy;
use App\Traits\PartnerField;

class SpecialOfferRewardCrudController extends CrudController
{
    use RelationFilter, CreatedAtFilters, PartnerFilter, EntityBinder, EntityCopy, PartnerField;

    private $searchableColumns = [
        'action_id',
    ];

    public function setUp()
    {
        $this->crud->setModel(SpecialOfferReward::class);
        $this->crud->setRoute('admin/specialOfferReward');
        $this->crud->setEntityNameStrings(__('special offer rewards'), __('special offers rewards'));
        $this->crud->addButton('line', 'copy', 'view', 'crud.buttons.copy', 'end');
        $this->crud->enableAjaxTable();
        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'brand',
                'label' => __('Brand'),
            ],
            [
                'name' => 'weight',
                'label' => __('Weight'),
            ],
            [
                'name' => 'image_url',
                'label' => __('Image URL'),
            ],
            [
                'name' => 'reward_id',
                'label' => __('Reward ID'),
            ],
        ]);

        $this->addCreatedAtFilters();

        $this->crud->addField([
            'name' => 'brand',
            'label' => __('brand'),
            'default' => '',
        ]);

        $this->crud->addField([
            'name' => 'reward_id',
            'label' => __('Reward ID'),
        ]);

        $this->crud->addField([
            'name' => 'weight',
            'label' => __('Weight'),
            'default' => '0',
        ]);

        $this->crud->addField([
            'name' => 'image',
            'label' => 'Image',
            'type' => 'upload',
            'upload' => true,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $redirect_location = parent::storeCrud();

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $redirect_location = parent::updateCrud();

        return $redirect_location;
    }

    public function edit($id)
    {
        $entry = is_scalar($id) ? $this->crud->getEntry($id) : $id;

        /**
         * @var \App\Models\SpecialOfferReward
         */
        $result = parent::edit($entry->id);

        return $result;
    }
}
