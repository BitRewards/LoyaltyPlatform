<?php

namespace App\Services;

use App\DTO\CustomBonusData;
use App\DTO\StoreEventData;
use App\DTO\CredentialData;
use App\Models\Code;
use App\Models\Partner;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class CodeService
{
    public function isTokenAvailable(Partner $partner, $token)
    {
        $code = Code::model()->findByPartnerAndToken($partner, $token);

        if (!$code) {
            return true;
        }

        if (!$code->user_id) {
            return true;
        }

        return false;
    }

    public function retrieveUserByToken(Partner $partner, $token)
    {
        $code = Code::model()->findByPartnerAndToken($partner, $token);

        if (!$code->user_id) {
            return null;
        }

        return $code->user;
    }

    public function findOrCreateCode(Partner $partner, $token)
    {
        $code = Code::model()->findByPartnerAndToken($partner, $token);

        if ($code) {
            return $code;
        }

        if (!$code) {
            $code = $this->createCode($partner, $token);
        }

        return $code;
    }

    public function createUserByToken(Partner $partner, $token)
    {
        $code = Code::model()->findByPartnerAndToken($partner, $token);

        if ($code && $code->user_id) {
            return $code->user;
        }

        \DB::beginTransaction();

        $user = app(UserService::class)->createClient(CredentialData::make(), $partner);

        if (!$code) {
            $code = $this->createCode($partner, $token);
        }
        $this->acquire($user, $code);

        \DB::commit();

        return $user;
    }

    public function createCode(Partner $partner, $token)
    {
        $code = new Code();
        $code->token = $token;
        $code->bonus_points = 0;
        $code->partner_id = $partner->id;
        $code->save();

        return $code;
    }

    public function acquire(User $user, Code $code)
    {
        if (!$code->user_id) {
            $this->acquireNobodysCode($user, $code);
        } else {
            if ($code->user->isEmptyUser()) {
                app(UserService::class)->merge($user, $code->user);
            } else {
                $userData = \HUser::getPersonalData($code->user);
                $userData = \HArray::convertToKeyValueStr($userData);
                $exception = \HValidator::createValidationException(__('This code has already been used by user: %s', $userData), 'token');

                throw $exception;
            }
        }
    }

    private function acquireNobodysCode(User $user, Code $code)
    {
        \DB::beginTransaction();
        $code->user_id = $user->id;
        $code->save();

        if ($code->bonus_points) {
            app(UserService::class)->giveCustomBonusToUser(
                new CustomBonusData($user, $code->bonus_points, \Auth::user(), null, $code)
            );
        }

        $code->acquired_at = Carbon::now();
        $code->save();

        \DB::commit();
    }

    /**
     * Detach given code from given user and reject associated transactions.
     *
     * @param Code        $code
     * @param User        $user
     * @param UserService $userService
     */
    public function detach(Code $code, User $user, UserService $userService)
    {
        \DB::transaction(function () use ($code, $user, $userService) {
            $transactions =
                Transaction
                    ::where('user_id', $user->id)
                    ->whereRaw("data->>'".StoreEventData::DATA_KEY_SOURCE_CODE_ID."' = ?", [$code->id])
                    ->get();

            foreach ($transactions as $transaction) {
                app(TransactionService::class)->updateTransactionStatus($transaction, Transaction::STATUS_REJECTED);
            }

            $code->user_id = null;
            $code->acquired_at = null;
            $code->save();
        });
    }
}
