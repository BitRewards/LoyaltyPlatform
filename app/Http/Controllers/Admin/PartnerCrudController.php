<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reward;
use App\Services\CustomizationsService;
use App\Crud\CrudController;
use App\Http\Requests\Admin\PartnerRequest;
use App\Models\Partner;
use App\Traits\Search;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use Illuminate\Http\Request;

class PartnerCrudController extends CrudController
{
    // FIXME pull request to backpack
    use Search, RelationFilter, CreatedAtFilters;

    private $searchableColumns = [
        'title',
        'email',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->crud->setModel(\App\Models\Partner::class);
        $this->crud->setRoute('admin/partner');
        $this->crud->setEntityNameStrings(__('partner'), __('partners'));
        $this->crud->enableAjaxTable();
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->allowAccess('login_as_partner');
        $this->crud->allowAccess('convert_to_fiat');
        $this->crud->addButton('line', 'login_as_partner', 'view', 'crud.buttons.login_as_partner', 'end');
        $this->crud->addButton('line', 'convert_to_fiat', 'view', 'crud.buttons.convert_to_fiat', 'end');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'title',
                'label' => __('Title'),
            ],
            [
                'name' => 'email',
                'label' => 'Email',
            ],
            [
                'name' => 'created_at',
                'label' => __('Created'),
            ],
        ]);

        $this->crud->addField([
            'name' => 'title',
            'label' => __('Title'),
        ]);

        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'text',
        ]);

        $this->crud->addField([
            'name' => 'url',
            'label' => 'URL',
            'type' => 'text',
        ]);

        $this->crud->addField([
            'name' => 'money_to_points_multiplier',
            'label' => __('How many points are in one money unit? Recommended setting: 100'),
            'type' => 'text',
        ]);

        $this->crud->addField([
            'label' => __('API token'),
            'name' => 'mainAdministratorApiKey',
            'type' => 'text',
            'attributes' => ['disabled' => 'disabled'],
        ]);

        $this->crud->addField([
            'label' => __('App URL'),
            'name' => 'appRootUrl',
            'type' => 'text',
            'attributes' => ['disabled' => 'disabled'],
        ]);

        $this->crud->addField([
            'label' => __('Language'),
            'name' => 'default_language',
            'type' => 'text',
            'attributes' => ['disabled' => 'disabled'],
        ]);
        /*
                $this->crud->addField([
                    'name' => 'customizations',
                    'label' => "Customizations",
                    'type' => 'json'
                ]);
        */
        $this->crud->addField([
            'name' => 'partner_settings',
            'label' => __('Settings'),
            'type' => 'json',
        ]);

        $this->crud->addField([
            'name' => 'customizations',
            'label' => __('Customizations'),
            'type' => 'partner_customizations',
        ]);

        $this->crud->addField([
            'name' => 'partner_group_role',
            'label' => __('Partner group role'),
            'type' => 'select_from_array',
            'options' => [
                'partner' => _('Partner'),
                'admin' => _('Administrator'),
            ],
        ]);

        $this->crud->addButton('top', 'mass_award', 'add', 'crud.buttons.mass_award', 'end');
    }

    public function store(PartnerRequest $request)
    {
        return parent::storeCrud();
    }

    public function edit($id)
    {
        parent::edit($id);

        return view('partners/edit', $this->data);
    }

    public function update(PartnerRequest $request)
    {
        return parent::updateCrud();
    }

    public function convertRewards($id)
    {
        $partner = Partner::model()->find($id);

        if (!$partner) {
            \Alert::info(__('Partner not found'))->flash();

            return redirect()->back();
        }

        try {
            \DB::transaction(function () use ($partner) {
                $partner->rewards()->where('price_type',
                    Reward::PRICE_TYPE_POINTS)->update(['price_type' => Reward::PRICE_TYPE_FIAT]);
            });

            \Alert::success(__('The price of the awards is converted'))->flash();
        } catch (\Throwable $e) {
            \Alert::error(__('An error has occurred'))->flash();
        }

        return redirect($this->crud->route);
    }

    public function loginAsPartner($id)
    {
        $currentUserId = \Auth::user()->id;
        \Session::put('previous_user_id', $currentUserId);

        \Auth::logout();

        $partner = Partner::model()->find($id);
        \Auth::login($partner->mainAdministrator, true);

        return redirect('/admin/');
    }

    /**
     * @param int                   $id
     * @param CustomizationsService $customizations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customizations($id, CustomizationsService $customizations)
    {
        $partner = Partner::find($id);

        if (is_null($partner)) {
            return response()->json(['data' => []]);
        }

        return response()->json([
            'data' => $customizations->customizationsFor($partner),
        ]);
    }

    /**
     * @param Request               $request
     * @param int                   $id
     * @param CustomizationsService $customizations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomizationSettings(Request $request, $id, CustomizationsService $customizations)
    {
        $partner = Partner::find($id);

        if (is_null($partner)) {
            return response()->json(['error' => 'Partner was not found'], 404);
        }

        $result = $customizations->updateCustomizationSettings($partner, $request->all());

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }
}
