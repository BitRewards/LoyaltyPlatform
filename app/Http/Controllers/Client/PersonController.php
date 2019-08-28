<?php
/**
 * PersonController.php
 * Creator: lehadnk
 * Date: 17/08/2018.
 */

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonAddEmailRequest;
use App\Http\Requests\PersonAddPhoneRequest;
use App\Http\Requests\PersonConfirmEmailRequest;
use App\Http\Requests\PersonConfirmPhoneRequest;
use App\Models\Credential;
use App\Models\CredentialValidationToken;
use App\Models\Person;
use App\Models\User;
use App\Services\EmailService;
use App\Services\Persons\CredentialGenerator;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonMerger;
use App\Services\Persons\UserGenerator;
use App\Services\SmsService;

class PersonController extends Controller
{
    /**
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var UserGenerator
     */
    private $userGeneratorService;

    /**
     * @var PersonMerger
     */
    private $personMergerService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var SmsService
     */
    private $smsService;

    /**
     * @var CredentialGenerator
     */
    private $credentialGeneratorService;

    public function __construct(
        PersonFinder $personFinderService,
        UserGenerator $userGeneratorService,
        PersonMerger $personMergerService,
        EmailService $emailService,
        SmsService $smsService,
        CredentialGenerator $credentialGeneratorService
    ) {
        $this->personFinderService = $personFinderService;
        $this->userGeneratorService = $userGeneratorService;
        $this->personMergerService = $personMergerService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->credentialGeneratorService = $credentialGeneratorService;
    }

    public function confirmEmail(PersonConfirmEmailRequest $request)
    {
        $this->emailService->sendPersonEmailConfirmation(
            $request->partner,
            \Auth::user()->person,
            \HUser::normalizeEmail($request->email)
        );

        return jsonResponse('ok');
    }

    public function confirmPhone(PersonConfirmPhoneRequest $request)
    {
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);

        $this->smsService->confirmPersonPhone(
            \Auth::user()->person,
            $phone
        );

        return jsonResponse('ok');
    }

    public function addEmail(PersonAddEmailRequest $request)
    {
        $email = \HUser::normalizeEmail($request->email);
        // Проверка токена
        if (!$token = CredentialValidationToken::validateEmail($email, $request->confirm_code)) {
            return jsonError(__('Invalid code from email!'));
        }
        $token->redeem();

        /**
         * We have 3 different cases here:
         * 1) This email belongs to another person, so we should merge them
         * 2) This email not exists, so we should add it as a credential
         * 3) This email exists and belongs to this person, so we should do nothing about that.
         */
        $person = $this->personFinderService->findByEmail($email, $request->partner->partner_group_id);

        if (null === $person) {
            $credential = $this->credentialGeneratorService->generateFromEmail(\Auth::user()->person, $email, $request->partner->partner_group_id);
            $credential->confirm();
        } else {
            if ($person->id !== \Auth::user()->person->id) {
                $this->personMergerService->mergePersonToPerson(\Auth::user()->person, $person);
            }
        }

        return jsonResponse('ok');
    }

    public function addPhone(PersonAddPhoneRequest $request)
    {
        // Проверка токена
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);

        if (!$token = CredentialValidationToken::validatePhone($phone, $request->confirm_sms)) {
            return jsonError(__('Incorrect code from SMS!'));
        }
        $token->redeem();

        $person = $this->personFinderService->findByPhone($phone, $request->partner->partner_group_id);

        if (null === $person) {
            $credential = $this->credentialGeneratorService->generateFromPhone(\Auth::user()->person, $phone, $request->partner->partner_group_id);
            $credential->confirm();
        } else {
            if ($person->id !== \Auth::user()->person->id) {
                $this->personMergerService->mergePersonToPerson(\Auth::user()->person, $person);
            }
        }

        return jsonResponse('ok');
    }
}
