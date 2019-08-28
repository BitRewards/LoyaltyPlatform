<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/21/18
 * Time: 1:42 AM.
 */

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\AuthenticationController\CheckEmailStatusRequest;
use App\Http\Requests\Client\AuthenticationController\CheckPhoneStatusRequest;
use App\Http\Requests\Client\AuthenticationController\LoginRequest;
use App\Http\Requests\Client\AuthenticationController\ProvideEmailRequest;
use App\Http\Requests\Client\AuthenticationController\ProvidePhoneRequest;
use App\Http\Requests\Client\AuthenticationController\SendEmailValidationTokenRequest;
use App\Http\Requests\Client\AuthenticationController\SendPasswordResetTokenRequest;
use App\Http\Requests\Client\AuthenticationController\SendPhoneValidationTokenRequest;
use App\Http\Requests\Client\AuthenticationController\ValidateEmailRequest;
use App\Http\Requests\Client\AuthenticationController\ValidatePhoneRequest;
use App\Http\Requests\Client\AuthenticationController\CreatePasswordRequest;

class FakeAuthenticationController
{
    public function checkEmailStatus(CheckEmailStatusRequest $request)
    {
        if ('lehadnk@gmail.com' === $request->email) {
            return JsonResponse([
                'action' => 'login',
            ]);
        }

        return JsonResponse([
            'action' => 'confirmEmail',
        ]);
    }

    public function checkPhoneStatus(CheckPhoneStatusRequest $request)
    {
        if ('+79588584406' === $request->phone) {
            return JsonResponse([
                'action' => 'login',
            ]);
        }

        return JsonResponse([
            'action' => 'confirmPhone',
        ]);
    }

    public function validateEmail(ValidateEmailRequest $request)
    {
        if ('12345' !== $request->token) {
            return JsonError([
                'message' => __('Mismatching token!'),
            ]);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function validatePhone(ValidatePhoneRequest $request)
    {
        if ('12345' !== $request->token) {
            return JsonError([
                'message' => __('Mismatching token!'),
            ]);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function createPassword(CreatePasswordRequest $request)
    {
        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function sendPhoneValidationToken(SendPhoneValidationTokenRequest $request)
    {
        if ('+79588584406' !== $request->phone) {
            return JsonError([
                'message' => __('Invalid phone'),
            ]);
        }

        return JsonResponse([
            'status' => 'valid',
            'action' => 'confirmPhone',
        ]);
    }

    public function sendEmailValidationToken(SendEmailValidationTokenRequest $request)
    {
        if ('lehadnk@gmail.com' !== $request->email) {
            return JsonError([
                'message' => __('Invalid email'),
            ]);
        }

        return JsonResponse([
            'status' => 'valid',
            'action' => 'confirmEmail',
        ]);
    }

    public function login(LoginRequest $request)
    {
        if ('12345' !== $request->password) {
            return JsonError([
                'message' => __('Invalid password'),
            ]);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function sendPasswordResetToken(SendPasswordResetTokenRequest $request)
    {
        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function providePhone(ProvideEmailRequest $request)
    {
        if ('12345' !== $request->token) {
            return JsonError([
                'message' => __('Mismatching token!'),
            ]);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }

    public function provideEmail(ProvidePhoneRequest $request)
    {
        if ('12345' !== $request->token) {
            return JsonError([
                'message' => __('Mismatching token!'),
            ]);
        }

        return JsonResponse([
            'status' => 'ok',
        ]);
    }
}
