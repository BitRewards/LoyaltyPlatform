<?php

namespace App\Services;

use App\Exceptions\ValidationConstraintException;
use App\Models\Partner;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class PhoneService
{
    const STATUS_NEW = 'new';
    const STATUS_USED = 'used';

    /**
     * @var Token
     */
    private $tokenModel;

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var SmsService
     */
    private $smsService;

    /**
     * @var Factory
     */
    private $validationFactory;

    public function __construct(
        Token $tokenModel,
        User $userModel,
        SmsService $smsService,
        Factory $validationFactory
    ) {
        $this->tokenModel = $tokenModel;
        $this->userModel = $userModel;
        $this->smsService = $smsService;
        $this->validationFactory = $validationFactory;
    }

    public function getPhoneStatus(Partner $partner, $phone): string
    {
        $user = $this->userModel::model()->findByPartnerAndPhone($partner, $phone);

        return $user ? self::STATUS_USED : self::STATUS_NEW;
    }

    public function sendPhoneConfirmation(User $user)
    {
        if ($user->isPhoneConfirmed()) {
            throw new ValidationConstraintException('phone', 'Phone number already confirmed');
        }

        $token = $this->tokenModel::add(
            $user->id,
            $this->tokenModel::TYPE_CONFIRM_PHONE,
            $user->phone,
            $this->tokenModel::DESTINATION_TYPE_PHONE
        );

        $confirmationMessage = __('You phone number confirmation code is: %s', $token);
        $result = $this->smsService->send($user->phone, $confirmationMessage);

        if (!$result) {
            throw new \RuntimeException(__('An error occurred during phone number confirmation!'));
        }
    }

    public function confirmPhone(User $user, string $token)
    {
        if ($user->isPhoneConfirmed()) {
            throw new ValidationConstraintException('phone', __('Phone already confirmed'));
        }

        $token = $this->tokenModel::check(
            $token,
            $this->tokenModel::TYPE_CONFIRM_PHONE,
            $this->tokenModel::DESTINATION_TYPE_PHONE,
            $user->phone,
            $user->id
        );

        if (!$token) {
            throw new ValidationConstraintException('token', __('Token not found'));
        }

        if (!$token->owner) {
            throw new ValidationConstraintException('token', __('Token owner not found'));
        }

        $user->phone_confirmed_at = Carbon::now();
        $user->saveOrFail();
    }

    public function sendResetPasswordNotification(Partner $partner, $phone)
    {
        $validator = $this->validationFactory->make(
            ['phone' => $phone],
            ['phone' => 'required|regex:/^[\+\(\)\-0-9]{6,20}$/']
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->userModel->findByPartnerAndPhone($partner, $phone);

        if (!$user) {
            throw new ValidationConstraintException('phone', __('Phone number not found'));
        }

        $token = $this->tokenModel::add(
            $user->id,
            Token::TYPE_RESET_PASSWORD,
            $user->phone,
            Token::DESTINATION_TYPE_PHONE
        );

        $this
            ->smsService
            ->send($user->phone, __('Your password reset code: %s', $token));
    }
}
