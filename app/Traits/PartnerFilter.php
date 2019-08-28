<?php

namespace App\Traits;

trait PartnerFilter
{
    public function addPartnerFilter()
    {
        if (\Auth::user()->can('admin')) {
            $this->crud->addColumn([
                'label' => __('Partner'),
                'type' => 'select',
                'name' => 'partner_id',
                'entity' => 'partner',
                'attribute' => 'title',
                'model' => \App\Models\Partner::class,
            ]);

            $this->addRelationFilter([
                'label' => '<i class="fa fa-user"></i> '.__('Partner'),
                'name' => 'partner_id',
                'foreign_key' => 'partner_id',
                'class' => \App\Models\Partner::class,
                'key' => 'id',
                'value' => 'title',
            ]);
        } else {
            $this->crud->addClause('where', 'partner_id', \Auth::user()->partner->id);
        }
    }
}
