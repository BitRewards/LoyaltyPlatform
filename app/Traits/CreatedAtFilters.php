<?php

namespace App\Traits;

trait CreatedAtFilters
{
    public function addCreatedAtFilters()
    {
        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'simple',
            'name' => 'today',
            'label' => '<i class="fa fa-calendar"></i> '.__('Today'),
        ],
        false,
        function () {
            $this->crud->addClause('where', 'created_at', '>=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('today'))));
            $this->crud->addClause('where', 'created_at', '<=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('today'))));
        });

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'simple',
            'name' => 'this_week',
            'label' => '<i class="fa fa-calendar"></i> '.__('This week'),
        ],
        false,
        function () {
            $this->crud->addClause('where', 'created_at', '>=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('first day of this week'))));
            $this->crud->addClause('where', 'created_at', '<=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('last day of this week'))));
        });

        $this->crud->addFilter([ // add a "simple" filter called Draft
            'type' => 'simple',
            'name' => 'this_month',
            'label' => '<i class="fa fa-calendar"></i> '.__('This month'),
        ],
        false,
        function () {
            $this->crud->addClause('where', 'created_at', '>=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('first day of this month'))));
            $this->crud->addClause('where', 'created_at', '<=', \Carbon\Carbon::createFromTimestamp(\HDate::adjust(strtotime('last day of this month'))));
        });
    }
}
