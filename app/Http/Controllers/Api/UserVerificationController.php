<?php

namespace App\Http\Controllers\Api;

use App\Services\SmsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendSmsConfirmation;
use App\Http\Requests\Api\VerifySmsConfirmation;

class UserVerificationController extends Controller
{
    /**
     * @param \App\Http\Requests\Api\SendSmsConfirmation $request
     * @param \App\Services\SmsService
     * @param string $userKey
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(SendSmsConfirmation $request, SmsService $smsService, $userKey)
    {
        $user = $request->getUserByKey();
        $smsService->confirmPhone($user->phone);

        return jsonResponse('ok');
    }

    /**
     * @param \App\Http\Requests\Api\VerifySmsConfirmation $request
     * @param \App\Services\SmsService
     * @param string $userKey
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(VerifySmsConfirmation $request, SmsService $smsService, $userKey)
    {
        $user = $request->getUserByKey();
        $result = $smsService->confirmPhoneFinish($user->phone, trim($request->token));

        return jsonResponse([
            'result' => $result,
        ]);
    }
}
