<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\CredentialData;
use App\Http\Controllers\ClientApiController;
use App\Http\Requests\CheckEmailVerificationCodeRequest;
use App\Http\Requests\ClientApi\LoginByFacebook;
use App\Http\Requests\SendEmailVerificationCodeRequest;
use App\Mail\BitRewards\LoginByEmailVerificationCode;
use App\Models\Partner;
use App\Models\Token;
use App\Models\User;
use App\Services\AuthTokenService;
use App\Services\EmailService;
use App\Services\OAuthService;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonGenerator;
use App\Services\Persons\UserGenerator;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TempAuthController extends ClientApiController
{
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var AuthTokenService
     */
    private $authTokenService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Token
     */
    private $tokenModel;

    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var PersonGenerator
     */
    private $personGeneratorService;

    /**
     * @var UserGenerator
     */
    private $userGeneratorService;

    public function __construct(
        UserTransformer $userTransformer,
        Auth $auth,
        UserService $userService,
        AuthTokenService $authTokenService,
        Token $tokenModel,
        Mail $mail,
        EmailService $emailService,
        PersonFinder $personFinderService,
        PersonGenerator $personGeneratorService,
        UserGenerator $userGeneratorService
    ) {
        $this->userTransformer = $userTransformer;
        $this->auth = $auth;
        $this->userService = $userService;
        $this->authTokenService = $authTokenService;
        $this->tokenModel = $tokenModel;
        $this->mail = $mail;
        $this->emailService = $emailService;
        $this->personFinderService = $personFinderService;
        $this->personGeneratorService = $personGeneratorService;
        $this->userGeneratorService = $userGeneratorService;
    }

    private function getMainPartner(): Partner
    {
        if (\HApp::isProduction()) {
            // BitRewards Marketplace partner
            return Partner::model()->where('key', '**REMOVED**')->first();
        } else {
            return Partner::model()->where('key', 'test-partner-key')->first();
        }
    }

    private function createUser(string $email)
    {
        $credentialData = CredentialData::createFromEmail($email);

        $person = $this->personFinderService->findByEmail($email, $this->getMainPartner()->partner_group_id);

        if (!$person) {
            $person = $this->personGeneratorService->makePerson($credentialData, $this->getMainPartner()->partner_group_id);
        }

        $user = $person->getUser($this->getMainPartner()->id);

        if (!$user) {
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $this->getMainPartner());
        }

        return $user;
    }

    private function findUser($email): ?User
    {
        $email = \HUser::normalizeEmail($email);
        $partner = $this->getMainPartner();

        $person = $this->personFinderService->findByEmail($email, $this->getMainPartner()->partner_group_id);

        if (!$person) {
            return null;
        }

        if (!$user = $person->getUser($partner->id)) {
            $credentialData = CredentialData::createFromEmail($email);
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $partner);
        }

        return $user;
    }

    public function sendEmailVerificationCode(SendEmailVerificationCodeRequest $request)
    {
        $email = \HUser::normalizeEmail($request->email);

        $user = $this->findUser($email);

        if (!$user) {
            $user = $this->createUser($email);
        }

        $token = $this->tokenModel::add(
            $user->id,
            Token::TYPE_LOGIN_BY_EMAIL,
            $user->email,
            Token::DESTINATION_TYPE_EMAIL
        );

        (new LoginByEmailVerificationCode($user, $token))->send(app(\Illuminate\Contracts\Mail\Mailer::class));

        $this->responseOk();
    }

    public function checkEmailVerificationCode(CheckEmailVerificationCodeRequest $request)
    {
        $user = $this->findUser($request->email);

        if (!$user) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, __('User not found'));
        }

        $token = $this->tokenModel::check(
            preg_replace('/[^0-9]+/', '', $request->code),
            $this->tokenModel::TYPE_LOGIN_BY_EMAIL,
            $this->tokenModel::DESTINATION_TYPE_EMAIL,
            $user->email,
            $user->id
        );

        if (!$token) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, __('The code is either wrong or expired'));
        }

        if (!$user->email_confirmed_at) {
            $user->email_confirmed_at = Carbon::now();
            $user->saveOrFail();
        }

        $authToken = $this->authTokenService->createToken($user, true);

        return $this->responseJson([
            'auth_token' => $authToken->token,
        ]);
    }

    public function loginByFacebook(LoginByFacebook $request)
    {
        $fbAccessToken = $request->access_token;
        $profile = app(OAuthService::class)->grabFbProfile($fbAccessToken);
        $partner = $this->getMainPartner();

        if (!$profile->email) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, __('Unable to login with Facebook without email provided'));
        }

        $credentialData = CredentialData::createFromSocialNetworkProfile($profile);

        $person = $this->personFinderService->findByFacebookId($profile->socialNetworkId, $this->getMainPartner()->partner_group_id);

        if (!$person) {
            $this->personGeneratorService->makePerson($credentialData, $partner->partner_group_id);
        }

        $user = $person->getUser($this->getMainPartner()->id);

        if (!$user) {
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $this->getMainPartner());
        }

        $authToken = $this->authTokenService->createToken($user, true);

        return $this->responseJson([
            'auth_token' => $authToken->token,
        ]);
    }
}
