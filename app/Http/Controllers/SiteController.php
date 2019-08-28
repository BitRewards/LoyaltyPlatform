<?php

namespace App\Http\Controllers;

use App\DTO\CredentialData;
use App\Services\Persons\AuthManagerService;
use App\Services\Persons\CredentialGenerator;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonGenerator;
use App\Services\Persons\PersonMerger;
use App\Services\Persons\UserGenerator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\OAuthService;
use App\Models\Partner;

class SiteController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var AuthManagerService
     */
    private $authService;

    /**
     * @var UserGenerator
     */
    private $userGeneratorService;

    /**
     * @var PersonGenerator
     */
    private $personGeneratorService;

    /**
     * @var PersonMerger
     */
    private $personMergerService;

    /**
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var CredentialGenerator
     */
    private $credentialGenerator;

    public function __construct(
        AuthManagerService $authService,
        UserGenerator $userGeneratorService,
        PersonGenerator $personGeneratorService,
        PersonMerger $personMergerService,
        PersonFinder $personFinderService,
        CredentialGenerator $credentialGenerator
    ) {
        $this->authService = $authService;
        $this->userGeneratorService = $userGeneratorService;
        $this->personGeneratorService = $personGeneratorService;
        $this->personMergerService = $personMergerService;
        $this->personFinderService = $personFinderService;
        $this->credentialGenerator = $credentialGenerator;
    }

    public function index(Request $request)
    {
        $hostname = mb_strtolower($request->getHttpHost());
        $url = false !== strpos($hostname, 'bitrewards') ? 'https://join.bitrewards.network' : 'https://giftd.tech/';

        return redirect($url);
    }

    public function loginViaOauth(Request $request)
    {
        $profile = app(OAuthService::class)->getSocialNetworkProfile($request);

        if (!$profile) {
            $url = \Session::get('_previous.url', config('app.url'));

            return redirect($url);
        }

        $partner = Partner::model()->findByKey($profile->partnerKey);

        $credentialData = CredentialData::createFromSocialNetworkProfile($profile);
        $person = $this->authService->getPersonByCredentials($credentialData, $partner);

        if (null === $person && null !== $credentialData->email) {
            $person = $this->personFinderService->findByEmail($credentialData->email, $partner->partner_group_id);

            if ($person) {
                $this->credentialGenerator->generateFromCredentialData($credentialData, $person, $partner->partner_group_id);
            }
        }

        if (null === $person) {
            if ($partner->isSignupDisabled()) {
                return view('loyalty/signup-disabled', ['email' => $credentialData->email ?? null]);
            } else {
                $person = $this->personGeneratorService->makePerson($credentialData, $partner->partner_group_id);
            }
        }

        $user = $person->getUser($partner->id);

        if (null === $user) {
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $partner);
        }

        \Auth::login($user);

        return view('loyalty/close', ['isEmailOrPhoneRequired' => false]);
    }

    public function mergeViaOauth(Request $request)
    {
        $profile = app(OAuthService::class)->getSocialNetworkProfile($request);
        $credentialData = CredentialData::createFromSocialNetworkProfile($profile);
        $partner = Partner::model()->findByKey($profile->partnerKey);
        $person = $this->authService->getPersonByCredentials($credentialData, $partner);

        if (!$person) {
            $this->credentialGenerator->generateFromCredentialData($credentialData, \Auth::user()->person, $partner->partner_group_id);
        } else {
            if ($person->id !== \Auth::user()->person->id) {
                $this->personMergerService->mergePersonToPerson(\Auth::user()->person, $person);
            }
        }

        return view('loyalty/close', ['isEmailOrPhoneRequired' => false]);
    }

    public function oauth(Request $request)
    {
        if (null !== \Auth::user()) {
            return $this->mergeViaOauth($request);
        }

        return $this->loginViaOauth($request);
    }

    public function adminOnly()
    {
        echo 'secret';
    }
}
