<?php

namespace App\Http\Controllers\Admin;

use App\DTO\CredentialData;
use App\Excel\UsersReport;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Imports\UsersImport;
use App\Models\Partner;
use App\Models\User;
use App\Services\UsersBulkImportService;
use App\Transformers\ActionTransformer;
use App\Crud\CrudController;
use App\Http\Requests\Admin\UserRequest as UpdateRequest;
use App\Http\Requests\Admin\UsersBulkImportRequest;
use App\Traits\Search;
use App\Traits\EntityBinder;
use App\Traits\RelationFilter;
use App\Traits\CreatedAtFilters;
use App\Traits\PartnerFilter;
use App\Http\Requests\Admin\GiveBonusRequest;
use App\Services\UserService;
use Spatie\Fractalistic\ArraySerializer;

class UserCrudController extends CrudController
{
    // FIXME pull request to backpack
    use EntityBinder, Search, RelationFilter, CreatedAtFilters, PartnerFilter;

    private $searchableColumns = [
        'id',
        'name',
        'email',
        'phone',
        'referral_promo_code',
    ];

    /**
     * @var ActionTransformer
     */
    private $actionTransformer;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(ActionTransformer $actionTransformer, UserService $userService)
    {
        $this->actionTransformer = $actionTransformer;
        $this->userService = $userService;
        parent::__construct();
    }

    public function setUp()
    {
        $this->crud->setModel(User::class);
        $this->crud->setRoute('admin/user');
        $this->crud->allowAccess('show');
        $this->crud->allowAccess('update');
        $this->crud->allowAccess('export');
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->setEntityNameStrings(__('user'), __('users'));
        $this->crud->enableAjaxTable();
        $this->crud->with('referrer');
        $this->crud->setCreateView('users.create');

        $isPartner = User::ROLE_PARTNER === \Auth::user()->role;

        if ($isPartner) {
            $this->crud->allowAccess('users_bulk_import');
            $this->crud->allowAccess('create');
            // $this->crud->allowAccess('delete');
        }

        $this->crud->addButton('top', 'users_bulk_import', 'view', 'crud.buttons.users_bulk_import', 'end');
        $this->crud->addButton('top', 'export', 'view', 'crud.buttons.export', 'end');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => __('ID'),
            ],
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
                'callback' => function (User $user) {
                    return \HAmount::pointsWithPartner($user->balance, $user->partner);
                },
                'type' => 'callback',
                'label' => __('Balance'),
                'name' => 'balance',
            ],
            [
                'name' => 'email',
                'label' => __('Email'),
            ],
            [
                'name' => 'phone',
                'label' => __('Phone'),
            ],
            [
                'name' => 'referrer_id',
                'label' => __('Referrer'),
                'type' => 'callback',
                'callback' => function (User $user) {
                    $referrer = $user->referrer;

                    if (is_null($referrer)) {
                        return '&mdash;';
                    }

                    return '<a href="'.url('/admin/user/'.$referrer->id).'">'.$referrer->getTitle().'</a>';
                },
            ],
            [
                'name' => 'created_at',
                'label' => __('Created'),
            ],
        ]);

        $this->crud->addColumn([
            'name' => 'referral_promo_code',
            'visibleInTable' => false,
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => __('Name'),
        ]);

        $this->crud->addField([
            'name' => 'picture',
            'label' => __('Avatar'),
            'type' => 'upload',
            'upload' => true,
            'disk' => 'selectel',
            'raw_url' => true,
            'presenter' => function ($url) {
                return '<img src="'.$url.'" alt="Avatar"/>';
            },
        ], 'update');

        if ($isPartner) {
            $partner = \Auth::user()->partner;

            if ($partner->isAuthMethodEmail()) {
                $this->crud->addField([
                    'name' => 'email',
                    'label' => __('Email'),
                ], 'create');
            }

            if ($partner->isAuthMethodPhone()) {
                $this->crud->addField([
                    'name' => 'phone',
                    'label' => __('Phone'),
                ], 'create');
            }

            $this->crud->addField([
                'name' => 'password',
                'label' => __('Password'),
            ], 'create');
        }

        $this->addCreatedAtFilters();
        $this->addPartnerFilter();
    }

    public function store(CreateUserRequest $request)
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('create');

        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }

        // insert item in the db
        $user = $this->userService->createClient(
            CredentialData::make(
                $request->except(['save_action', '_token', '_method', 'current_tab', 'http_referrer'])
            ),
            \Auth::user()->partner
        );

        if ($user->email) {
            $this->userService->confirmEmailFor($user);
        }

        if ($user->phone) {
            $this->userService->confirmPhoneFor($user);
        }

        $this->data['entry'] = $this->crud->entry = $user;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->setSaveAction();

        return $this->performSaveAction($user->getKey());
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $entry
     *
     * @return \Illuminate\Http\Response
     */
    public function show($entry)
    {
        $this->crud->hasAccessOrFail('show');

        // get the info for that entry
        $this->data['entry'] = $entry;
        $this->data['crud'] = $this->crud;
        $this->data['title'] = trans('backpack::crud.preview').' '.$this->crud->entity_name;

        $bonusActions = $entry->partner->customBonusActions();
        $this->data['customBonusActions'] = fractal($bonusActions, $this->actionTransformer)
            ->serializeWith(new ArraySerializer())
            ->toArray();

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('users.show', $this->data);
    }

    public function giveBonus(GiveBonusRequest $request)
    {
        $bonus = intval($request->input('bonus'));

        if ($bonus <= 0) {
            \Alert::warning(__('The points amount needs to be more than 0!'))->flash();

            return redirect()->back();
        }

        app(UserService::class)->giveCustomBonusToUser($request->getDto());

        \Alert::success(__('Points successfully added!'))->flash();

        return redirect()->back();
    }

    public function createBulk()
    {
        return view('users.bulk');
    }

    public function previewBulk(UsersBulkImportRequest $request)
    {
        if ($request->file) {
            $users = app(UsersBulkImportService::class)->getBulkUsersFromArray(
                \Auth::user()->partner,
                \Excel::toArray(new UsersImport(), $request->file, null),
                UsersBulkImportService::PREVIEW_USERS_COUNT
            );
        }

        if ($request->data) {
            $users = app(UsersBulkImportService::class)->getBulkUsers(
                \Auth::user()->partner,
                $request->data,
                UsersBulkImportService::PREVIEW_USERS_COUNT
            );
        }

        return view('users._bulk_preview', compact('users'));
    }

    public function storeBulk(UsersBulkImportRequest $request)
    {
        $usersBulkImport = app(UsersBulkImportService::class)->prepareImport(
            $request->data ?? \Excel::toArray(new UsersImport(), $request->file, null),
            $request->title,
            $request->mode,
            \Auth::user()->partner
        );

        if ($usersBulkImport) {
            app(UsersBulkImportService::class)->import($usersBulkImport);

            \Alert::success(__("User processing started, we'll send you an email as soon as it's finished"))->flash();
        } else {
            \Alert::error(__('An error occurred while importing users'))->flash();
        }

        return redirect(route('admin.user.createBulk'));
    }

    public function export()
    {
        /** @var Partner $partner */
        $partner = \Auth::user()->partner;

        if (!$partner) {
            return abort(403);
        }

        return \Excel::download(new UsersReport($partner), 'users.xlsx');
    }
}
