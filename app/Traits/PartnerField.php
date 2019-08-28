<?php

namespace App\Traits;

trait PartnerField
{
    public function addPartnerField()
    {
        if (\Auth::user()->can('admin')) {
            $this->crud->addField([
                'name' => 'partner_id',
                'label' => __('Partner'),
                'type' => 'select2',
                'entity' => 'partner',
                'attribute' => 'title',
                'model' => \App\Models\Partner::class,
            ]);
        } else {
            $this->crud->addField([
                'name' => 'partner_id',
                'type' => 'hidden',
                'default' => \Auth::user()->partner->id ?? null,
            ]);
        }
    }
}
