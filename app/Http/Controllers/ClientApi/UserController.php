<?php

namespace App\Http\Controllers\ClientApi;

use App\Exceptions\ValidationConstraintException;
use App\Http\Controllers\ClientApiController;
use App\Http\Requests\BitrewardsUpdateWalletRequest;
use App\Http\Requests\Client\AuthenticationController\CheckEmailStatusRequest;
use App\Http\Requests\Client\AuthenticationController\CheckPhoneStatusRequest;
use App\Http\Requests\ConfirmPhoneRequest;
use App\Models\Partner;
use App\Models\User;
use App\Services\EmailService;
use App\Services\PhoneService;
use App\Services\ReferralStatisticService;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends ClientApiController
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
     * @var EmailService
     */
    private $emailService;

    /**
     * @var PhoneService
     */
    private $phoneService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ReferralStatisticService
     */
    private $referralStatisticService;

    public function __construct(
        UserTransformer $userTransformer,
        Auth $auth,
        EmailService $emailService,
        PhoneService $phoneService,
        UserService $userService,
        ReferralStatisticService $referralStatisticService
    ) {
        $this->userTransformer = $userTransformer;
        $this->auth = $auth;
        $this->emailService = $emailService;
        $this->phoneService = $phoneService;
        $this->userService = $userService;
        $this->referralStatisticService = $referralStatisticService;
    }

    public function me(): JsonResponse
    {
        return $this->responseJson(fractal($this->auth::user(), $this->userTransformer));
    }

    public function emailStatus(CheckEmailStatusRequest $request): JsonResponse
    {
        $email = \HUser::normalizeEmail($request->email);
        $status = $this->emailService->getEmailStatus($request->partner, $email);
        $response = [
            'status' => $status,
        ];

        if (EmailService::STATUS_USED === $status) {
            $response += [
                'message' => __('Email already exist'),
            ];
        }

        return $this->responseJson($response);
    }

    public function isEmailConfirmed(): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();

        return $this->responseJson([
            'emailConfirmed' => $user->isEmailConfirmed(),
        ]);
    }

    public function sendEmailConfirmation()
    {
        /** @var User $user */
        $user = $this->auth::user();

        try {
            $this->emailService->sendUserEmailConfirmation($user);
        } catch (\Exception $e) {
            return $this->serverError(__('An error occurred during email confirmation!'));
        }

        return $this->responseOk();
    }

    public function confirmEmail(Request $request)
    {
        /** @var User $user */
        $user = $this->auth::user();
        $token = $request->get('token');

        try {
            $this->emailService->confirmEmail($user, $token);
        } catch (\Exception $e) {
            //@todo error log
            return $this->serverError(__('Email confirmation failed'));
        }

        return $this->responseOk();
    }

    public function phoneStatus(CheckPhoneStatusRequest $request): JsonResponse
    {
        $phone = \HUser::normalizePhone($request->phone, $request->partner->default_country);
        $userPhoneStatus = $this->phoneService->getPhoneStatus($request->partner, $phone);
        $response = [
            'status' => $userPhoneStatus,
        ];

        if (PhoneService::STATUS_USED === $userPhoneStatus) {
            $response['message'] = __('Phone already used!');
        }

        return $this->responseJson($response);
    }

    public function sendPhoneConfirmation()
    {
        /** @var User $user */
        $user = $this->auth::user();
        $this->phoneService->sendPhoneConfirmation($user);

        return $this->responseOk();
    }

    public function confirmPhone(ConfirmPhoneRequest $request)
    {
        /** @var User $user */
        $user = $this->auth::user();
        $token = $request->get('token');
        $this->phoneService->confirmPhone($user, $token);

        return $this->responseOk();
    }

    public function resetPassword(Request $request, Partner $partner): Response
    {
        if ($partner->isAuthMethodPhone()) {
            return $this->processResetPasswordByPhone($partner, $request->emailOrPhone);
        }

        return $this->processResetPasswordByEmail($partner, $request->emailOrPhone);
    }

    public function resetPasswordByEmail(Request $request, Partner $partner): Response
    {
        return $this->processResetPasswordByEmail($partner, $request->email);
    }

    public function resetPasswordByPhone(Request $request, Partner $partner): Response
    {
        return $this->processResetPasswordByPhone($partner, $request->phone);
    }

    protected function processResetPasswordByEmail(Partner $partner, string $email): Response
    {
        $this->emailService->sendResetPasswordNotification($partner, $email);

        return $this->responseOk();
    }

    protected function processResetPasswordByPhone(Partner $partner, $phone): Response
    {
        $phone = \HUser::normalizePhone($phone, $partner->default_country);

        $this->phoneService->sendResetPasswordNotification($partner, $phone);

        return $this->responseOk();
    }

    public function confirmResetPassword(Request $request, Partner $partner): Response
    {
        $this->userService->confirmResetPassword(
            $partner,
            $request->emailOrPhone,
            $request->token,
            $request->password
        );

        return $this->responseOk();
    }

    public function updateBitTokenAddress(BitrewardsUpdateWalletRequest $request): Response
    {
        /** @var User $user */
        $user = $this->auth::user();

        try {
            $this->userService->updateBitTokenAddress($user, $request->ethereum_wallet);
        } catch (\InvalidArgumentException $e) {
            throw new ValidationConstraintException('ethereum_wallet', $e->getMessage());
        }

        return $this->responseOk();
    }

    public function updateEthereumAddress(BitrewardsUpdateWalletRequest $request): Response
    {
        /** @var User $user */
        $user = $this->auth::user();

        try {
            $this->userService->updateEthereumAddress($user, $request->ethereum_wallet);
        } catch (\InvalidArgumentException $e) {
            throw new ValidationConstraintException('ethereum_wallet', $e->getMessage());
        }

        return $this->responseOk();
    }

    public function referralStatistic(Partner $partner, Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        $statisticData = $this
            ->referralStatisticService
            ->getReferralStatistic(
                $partner,
                new \DateTime($request->get('from')),
                new \DateTime($request->get('to')),
                Auth::user()
            );

        return $this->responseJson($statisticData);
    }
}
