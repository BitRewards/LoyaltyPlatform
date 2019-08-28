<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 1:42 AM.
 */

namespace App\Http\Controllers\Client;

use App\DTO\CredentialData;
use App\Http\Requests\Client\AuthenticationController\CheckEmailStatusRequest;
use App\Http\Requests\Client\AuthenticationController\CheckPhoneStatusRequest;
use App\Http\Requests\Client\AuthenticationController\LoginRequest;
use App\Http\Requests\Client\AuthenticationController\ProvideEmailRequest;
use App\Http\Requests\Client\AuthenticationController\ProvidePhoneRequest;
use App\Http\Requests\Client\AuthenticationController\SendEmailValidationTokenRequest;
use App\Http\Requests\Client\AuthenticationController\SendPasswordResetTokenRequest;
use App\Http\Requests\Client\AuthenticationController\SendPhoneValidationTokenRequest;
use App\Http\Requests\Client\AuthenticationController\SetPasswordRequest;
use App\Http\Requests\Client\AuthenticationController\ValidateEmailRequest;
use App\Http\Requests\Client\AuthenticationController\ValidatePhoneRequest;
use App\Models\Credential;
use App\Models\CredentialValidationToken;
use App\Services\EmailService;
use App\Services\Persons\AuthManagerService;
use App\Services\Persons\CredentialGenerator;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonGenerator;
use App\Services\Persons\PersonMerger;
use App\Services\Persons\UserGenerator;
use App\Services\SmsService;

class AuthenticationController
{
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
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var SmsService
     */
    private $smsService;

    /**
     * @var SmsService
     */
    private $personMergerService;

    /**
     * @var CredentialGenerator
     */
    private $credentialGeneratorService;

    public function __construct(
        AuthManagerService $authService,
        UserGenerator $userGeneratorService,
        PersonGenerator $personGeneratorService,
        PersonFinder $personFinderService,
        EmailService $emailService,
        SmsService $smsService,
        PersonMerger $personMergerService,
        CredentialGenerator $credentialGeneratorService
    ) {
        $this->authService = $authService;
        $this->userGeneratorService = $userGeneratorService;
        $this->personGeneratorService = $personGeneratorService;
        $this->personFinderService = $personFinderService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->personMergerService = $personMergerService;
        $this->credentialGeneratorService = $credentialGeneratorService;
    }

    public function checkEmailStatus(CheckEmailStatusRequest $request)
    {
        $email = \HUser::normalizeEmail($request->email);
        $person = $this->personFinderService->findByEmail($email, $request->partner->partner_group_id);

        if ($person && $person->isEmailConfirmed($email)) {
            return JsonResponse([
                'action' => 'login',
            ]);
        }

        if ($request->partner->isSignupDisabled()) {
            return JsonResponse([
                'action' => 'signupDisabled',
            ]);
        }

        // Sending token
        $this->emailService->sendGuestEmailConfirmation($request->partner, $email);

        return JsonResponse([
            'action' => 'confirmEmail',
        ]);
    }

    public function checkPhoneStatus(CheckPhoneStatusRequest $request)
    {
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);
        $person = $this->personFinderService->findByPhone($phone, $request->partner->partner_group_id);

        if ($person && $person->isPhoneConfirmed($phone)) {
            return JsonResponse([
                'action' => 'login',
            ]);
        }

        if ($request->partner->isSignupDisabled()) {
            return JsonResponse([
                'action' => 'signupDisabled',
            ]);
        }

        // Sending confirmation token via sms
        $this->smsService->confirmGuestPhone($phone);

        return JsonResponse([
            'action' => 'confirmPhone',
        ]);
    }

    public function validateEmail(ValidateEmailRequest $request)
    {
        $email = \HUser::normalizeEmail($request->email);

        if (!$token = CredentialValidationToken::validateEmail($email, $request->token)) {
            return JsonError([
                'message' => __('Invalid code from email!'),
            ]);
        }

        $token->redeem();

        if (!$user = \Auth::user()) {
            // This is a new user
            $credentialData = CredentialData::createFromEmail($email);
            $user = $this->authService->findOrCreatePersonAndUser($credentialData, $request->partner, true);
            $user->person->confirmEmail($email);
            \Auth::login($user);
        } else {
            if (0 === $user->person->credentials->where('email', '=', $email)->count()) {
                $credential = $this->credentialGeneratorService->generateFromEmail($user->person, $email, $request->partner->partner_group_id);
                $credential->confirm();
            } else {
                $user->person->confirmEmail($email);
            }
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function validatePhone(ValidatePhoneRequest $request)
    {
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);

        if (!$token = CredentialValidationToken::validatePhone($phone, $request->token)) {
            return JsonError([
                'message' => __('Incorrect code from SMS!'),
            ]);
        }

        $token->redeem();

        if (!$user = \Auth::user()) {
            // This is a new user
            $credentialData = CredentialData::createFromPhone($phone);
            $user = $this->authService->findOrCreatePersonAndUser($credentialData, $request->partner, true);
            $user->person->confirmPhone($phone);
            \Auth::login($user);
        } else {
            if (0 === $user->person->credentials->where('phone', '=', $phone)->count()) {
                $credential = $this->credentialGeneratorService->generateFromPhone($user->person, $phone, $request->partner->partner_group_id);
                $credential->confirm();
            } else {
                $user->person->confirmPhone($phone);
            }
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function setPassword(SetPasswordRequest $request)
    {
        $user = \Auth::user();

        /**
         * @var Credential
         */
        $credential = null;

        if (null !== $request->phone) {
            $credential = $user->person->credentials
                ->where('phone', '=', \HUser::normalizePhone($request->phone, $request->partner->default_country))
                ->first();
        }

        if (null !== $request->email) {
            $credential = $user->person->credentials
                ->where('email', '=', \HUser::normalizeEmail($request->email))
                ->first();
        }

        if (null === $credential) {
            return JsonError([
                'message' => __('No credentials matching input found!'),
            ]);
        }

        $credential->setPassword($request->password);
        $credential->save();

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function sendPhoneValidationToken(SendPhoneValidationTokenRequest $request)
    {
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);
        $this->smsService->confirmPersonPhone(\Auth::user()->person, $phone);

        return JsonResponse([
            'status' => 'valid',
            'action' => 'confirmPhone',
        ]);
    }

    public function sendEmailValidationToken(SendEmailValidationTokenRequest $request)
    {
        $email = \HUser::normalizeEmail($request->email);
        $this->emailService->sendPersonEmailConfirmation($request->partner, \Auth::user()->person, $email);

        return JsonResponse([
            'status' => 'valid',
            'action' => 'confirmEmail',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentialData = CredentialData::make([]);
        $credentialData->phone = $request->phone ? \HUser::normalizePhone($request->phone, $request->partner->default_country) : null;
        $credentialData->email = $request->email ? \HUser::normalizeEmail($request->email) : null;
        $credentialData->password = $request->password;
        $credentialData->referrer_id = $request->referrer_id ?: null;
        $credentialData->referrer_key = $request->referrer_key ?: null;

        $person = $this->authService->getPersonByCredentials($credentialData, $request->partner);

        if (null === $person) {
            return JsonError(['password' => __('The password is invalid')]);
        }

        $user = $person->getUser($request->partner->id);

        if (null === $user) {
            $user = $this->userGeneratorService->fromCredentialDataForPerson($credentialData, $person, $request->partner);
        }

        \Auth::login($user, true);

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function sendPasswordResetToken(SendPasswordResetTokenRequest $request)
    {
        $credential = null;

        if (null !== $request->phone) {
            $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);
            $person = $this->personFinderService->findByPhone($phone, $request->partner->partner_group_id);

            if (!$person) {
                return JsonError([
                    'message' => __('No user with such email found!'),
                ]);
            }

            $this->smsService->confirmPersonPhone($person, $phone);
        }

        if (null !== $request->email) {
            $email = \HUser::normalizeEmail($request->email);
            $person = $this->personFinderService->findByEmail($email, $request->partner->partner_group_id);

            if (!$person) {
                return JsonError([
                    'message' => __('No user with such email found!'),
                ]);
            }

            $this->emailService->sendPersonEmailConfirmation($request->partner, $person, $email);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function providePhone(ProvidePhoneRequest $request)
    {
        $user = \Auth::user();
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);

        if (!$token = CredentialValidationToken::validatePhone($phone, $request->token)) {
            return JsonError([
                'message' => __('Incorrect code from SMS!'),
            ]);
        }

        $token->redeem();

        if (0 === $user->person->credentials->where('phone', '=', $phone)->count()) {
            $credential = new Credential();
            $credential->type_id = Credential::TYPE_PHONE;
            $credential->phone = $phone;
            $user->person->addCredentials($credential);
            $credential->confirm();
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function provideEmail(ProvideEmailRequest $request)
    {
        $user = \Auth::user();
        $email = \HUser::normalizeEmail($request->email);

        if (!$token = CredentialValidationToken::validateEmail($email, $request->token)) {
            return JsonError([
                'message' => __('Invalid code from email!'),
            ]);
        }

        $token->redeem();

        if (0 === $user->person->credentials->where('email', '=', $email)->count()) {
            $credential = new Credential();
            $credential->type_id = Credential::TYPE_EMAIL;
            $credential->email = $email;
            $user->person->addCredentials($credential);
            $credential->confirm();
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }
}
