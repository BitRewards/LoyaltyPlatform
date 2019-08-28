<?php

namespace App\Services;

use App\Exceptions\ValidationConstraintException;
use App\Mail\ConfirmAddEmail;
use App\Mail\ConfirmEmail;
use App\Mail\GuestEmailConfirmation;
use App\Mail\ResetPassword;
use App\Models\CredentialValidationToken;
use App\Models\Partner;
use App\Models\Person;
use App\Models\Token;
use App\Models\User;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailService
{
    const STATUS_NEW = 'new';
    const STATUS_NEED_MERGE = 'need-merge';
    const STATUS_USED = 'used';

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var Token
     */
    private $tokenModel;

    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var Factory
     */
    private $validationFactory;

    public function __construct(User $userModel, Token $tokenModel, Mail $mail, Factory $validationFactory)
    {
        $this->userModel = $userModel;
        $this->tokenModel = $tokenModel;
        $this->mail = $mail;
        $this->validationFactory = $validationFactory;
    }

    public function getEmailStatus(Partner $partner, string $email): string
    {
        //@todo move findByPartnerAndEmail userService
        $user = $this->userModel->findByPartnerAndEmail($partner, $email);

        if (null === $user) {
            return self::STATUS_NEW;
        }

        return empty($user->password) ? self::STATUS_NEED_MERGE : self::STATUS_USED;
    }

    public function sendUserEmailConfirmation(User $user)
    {
        if ($user->isEmailConfirmed()) {
            throw new ValidationConstraintException('email', __('Email already confirmed'));
        }

        $token = $this->tokenModel::add(
            $user->id,
            Token::TYPE_CONFIRM_EMAIL,
            $user->email,
            Token::DESTINATION_TYPE_EMAIL
        );

        $this->mail::queue(new ConfirmEmail($user, $token));
    }

    public function sendGuestEmailConfirmation(Partner $partner, $email)
    {
        $token = new CredentialValidationToken();
        $token->email = $email;
        $token->attemptGeneration(function () {
            return random_digit_code(6);
        });

        $this->mail::queue(new GuestEmailConfirmation($partner, $email, $token->token));
    }

    public function sendPersonEmailConfirmation(Partner $partner, Person $person, string $email)
    {
        $token = new CredentialValidationToken();
        $token->person_id = $person->id;
        $token->email = $email;
        $token->attemptGeneration(function () {
            return random_digit_code(6);
        });

        $this->mail::queue(new ConfirmAddEmail($partner, $person, $email, $token->token));
    }

    public function confirmEmail(User $user, string $confirmToken)
    {
        $token = $this->tokenModel::check(
            $confirmToken,
            $this->tokenModel::TYPE_CONFIRM_EMAIL,
            $this->tokenModel::DESTINATION_TYPE_EMAIL,
            $user->email,
            $user->id
        );

        if (!$token) {
            throw new \RuntimeException('Token not found');
        }

        if (!$token->owner) {
            throw new \RuntimeException('Token owner not found');
        }

        /** @var User $user */
        $user = $token->owner;

        $user->person->confirmEmail($user->email);
    }

    public function sendResetPasswordNotification(Partner $partner, string $email)
    {
        $validator = $this->validationFactory->make(
            ['email' => $email],
            ['email' => 'required|email']
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = $this->userModel->findByPartnerAndEmail($partner, $email);

        if (!$user) {
            throw new ValidationConstraintException('email', __('Email address not found'));
        }

        $token = $this->tokenModel::add(
            $user->id,
            Token::TYPE_RESET_PASSWORD,
            $user->email,
            Token::DESTINATION_TYPE_EMAIL
        );
        $this->mail::queue(new ResetPassword($user, $token));
    }
}
