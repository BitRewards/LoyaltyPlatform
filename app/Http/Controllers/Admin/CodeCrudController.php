<?php

namespace App\Http\Controllers\Admin;

use App\Crud\CrudController;
use App\Http\Requests\Admin\CodeRequest as StoreRequest;
use App\Http\Requests\Admin\CodeRequest as UpdateRequest;
use App\Http\Requests\Admin\CodesBulkImportRequest;
use App\Traits\Search;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Traits\EntityBinder;
use App\Traits\EntityCopy;
use App\Traits\PartnerField;
use App\Models\Code;
use App\Models\Partner;

class CodeCrudController extends CrudController
{
    use Search, RelationFilter, CreatedAtFilters, PartnerFilter, EntityBinder, EntityCopy, PartnerField;

    private $searchableColumns = ['token'];

    public function setUp()
    {
        $this->crud->setModel("App\Models\Code");
        $this->crud->setRoute('admin/code');
        $this->crud->setEntityNameStrings(__('promo code'), __('promo codes'));
        $this->crud->allowAccess('codes_bulk_import');
        $this->crud->addButton('line', 'copy', 'view', 'crud.buttons.copy', 'end');
        $this->crud->addButton('top', 'codes_bulk_import', 'view', 'crud.buttons.codes_bulk_import', 'end');
        $this->crud->enableAjaxTable();

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ], [
                'name' => 'token',
                'label' => __('Code'),
            ], [
                'name' => 'bonus_points',
                'label' => __('Bonus points'),
            ],
            [
                'name' => 'user_id',
                'label' => __('User ID'),
            ],
        ]);

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();

        $this->crud->addField([
            'name' => 'token',
            'label' => __('Code'),
        ]);

        $this->crud->addField([
            'name' => 'bonus_points',
            'label' => __('Bonus points'),
            'type' => 'text',
        ]);

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

    public function createBulk()
    {
        return view('codes/bulk');
    }

    public function storeBulk(CodesBulkImportRequest $request)
    {
        $addedCount = 0;
        $updatedCount = 0;
        $ignoredCount = 0;
        $tokens = \HStr::splitByNewLine($request->tokens);

        foreach ($tokens as $token) {
            $code = Code::model()->findByPartnerAndToken(Partner::find($request->partner_id), $token);

            if ($code) {
                if (null == $code->acquired_at) {
                    $code->bonus_points = $request->bonus_points;
                    $code->save();

                    ++$updatedCount;
                } else {
                    ++$ignoredCount;
                }
            } else {
                $code = new Code();
                $code->partner_id = $request->partner_id;
                $code->bonus_points = $request->bonus_points;
                $code->token = $token;
                $code->save();

                ++$addedCount;
            }
        }

        $message = __('%s codes added, %s codes updated, %s codes ignored', $addedCount, $updatedCount, $ignoredCount);
        \Alert::success($message)->flash();

        return redirect(route('admin.code.createBulk'));
    }
}
