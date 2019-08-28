<?php

namespace App\Http\Controllers\Admin;

use App\Administrator;
use App\Http\Requests\Admin\CreateHelpItemRequest;
use App\Http\Requests\Admin\UpdateHelpItemRequest;
use App\Models\HelpItem;
use App\Models\User;
use App\Traits\CreatedAtFilters;
use App\Traits\EntityBinder;
use App\Traits\PartnerFilter;
use App\Traits\Search;
use App\Crud\CrudController;

class HelpItemsCrudController extends CrudController
{
    use EntityBinder, Search, CreatedAtFilters, PartnerFilter;

    public function setup()
    {
        $this->crud->setModel(HelpItem::class);
        $this->crud->setRoute('admin/help-items');
        $this->crud->allowAccess(['edit', 'update']);
        $this->crud->denyAccess('delete');
        $this->crud->setEntityNameStrings(__('question'), __('questions'));
        $this->crud->enableAjaxTable();

        $isPartner = Administrator::ROLE_PARTNER === \Auth::user()->role;

        if ($isPartner) {
            $this->crud->allowAccess('delete');
        }

        $this->crud->setColumns([
            ['name' => 'id', 'label' => __('ID')],
            ['name' => 'question', 'label' => __('Question')],
            [
                'name' => 'answer',
                'label' => __('Answer'),
                'type' => 'callback',
                'callback' => function (HelpItem $helpItem) {
                    return str_limit($helpItem->answer, 50);
                },
            ],
            ['name' => 'position', 'label' => __('Position')],
        ]);

        $fields = collect([
            'question' => ['label' => __('Question')],
            'answer' => ['label' => __('Answer'), 'type' => 'textarea'],
            'position' => ['label' => __('Position')],
        ]);

        $fields->each(function (array $field, string $name) {
            $this->crud->addField(array_merge(['name' => $name], $field));
        });

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();

        $this->crud->addClause('where', 'language', \HLanguage::getCurrent());
    }

    /**
     * @param CreateHelpItemRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateHelpItemRequest $request)
    {
        $response = parent::storeCrud($request);

        $this->crud->entry->update([
            'partner_id' => $request->user()->partner_id,
            'language' => \HLanguage::getCurrent(),
        ]);

        return $response;
    }

    /**
     * @param UpdateHelpItemRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateHelpItemRequest $request)
    {
        return parent::updateCrud($request);
    }

    /**
     * @param int $entity
     *
     * @return \Illuminate\Http\Response|string
     */
    public function destroy($entity)
    {
        if (is_integer($entity)) {
            $entity = HelpItem::find($entity);
        }

        if (!\Auth::user()->can('destroy', $entity)) {
            return abort(403);
        }

        return parent::destroy($entity->id);
    }
}
