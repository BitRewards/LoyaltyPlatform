<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreatePartnerByPartnerRequest;
use App\Http\Requests\Api\SetPasswordForPartnerRequest;
use App\Models\Partner;
use App\Services\Giftd\ApiClient;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Services\PartnerService;
use App\Http\Controllers\Controller;
use App\Transformers\PartnerTransformer;
use App\Http\Requests\Api\CreatePartnerRequest;
use App\Http\Requests\Api\ChangeLanguageRequest;
use App\Http\Requests\Api\ChangeCurrencyRequest;

class PartnerController extends Controller
{
    /**
     * Get the partner profile.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(
            fractal($request->user()->partner, new PartnerTransformer())
        );
    }

    public function createFromGiftd(CreatePartnerRequest $request)
    {
        /*
         * @var Partner
         */
        $existingPartner = Partner::where('giftd_id', $request->giftd_id)->first();

        if ($existingPartner) {
            return [
                'api_token' => $existingPartner->mainAdministrator->api_token,
                'key' => $existingPartner->key,
                'password' => '***',
            ];
        }

        ['partner' => $partner, 'password' => $password] = app(PartnerService::class)->signupPartner($request);

        return [
            'api_token' => $partner->mainAdministrator->api_token,
            'key' => $partner->key,
            'password' => $password,
        ];
    }

    public function setPassword(SetPasswordForPartnerRequest $request)
    {
        $partner = \App\Models\Partner::whereGiftdId($request->giftd_id)->first();

        if (!$partner) {
            abort(404);
        }
        app(UserService::class)->setPasswordForPartner($partner, $request->password);

        return [
            'result' => 'ok',
        ];
    }

    public function getSignupBonus(Request $request)
    {
        $partner = $request->user()->partner;
        $result = $partner->getSignupBonus();

        return [
            'value' => $result,
        ];
    }

    public function changeLanguage(ChangeLanguageRequest $request)
    {
        $partner = $request->user()->partner;
        $result = app(PartnerService::class)->changeLanguage($partner, $request->default_language);

        return response()->json(
            [
                'default_language' => $result->default_language,
            ]
        );
    }

    public function changeCurrency(ChangeCurrencyRequest $request)
    {
        $partner = $request->user()->partner;
        $result = app(PartnerService::class)->changeCurrency($partner, $request->currency);

        return response()->json(
            [
                'currency' => $result->currency,
            ]
        );
    }

    public function create(CreatePartnerByPartnerRequest $request)
    {
        $apiClient = new ApiClient(
            config('giftd.admin.user_id'),
            config('giftd.admin.api_key')
        );

        $parentPartner = $request->user()->partner;

        $apiClient->queryCrm('partner/createPartner', [
            'email' => $request->email,
            'currency' => $parentPartner->currency,
            'default_language' => $parentPartner->default_language,
            'title' => $request->title,
            'partner_group_id' => $parentPartner->partner_group_id,
        ]);
    }
}
