<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\PartnerWalletFactory;
use App\Http\Controllers\ClientApiController;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WalletController extends ClientApiController
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var PartnerWalletFactory
     */
    protected $partnerWalletFactory;

    public function __construct(Auth $auth, PartnerWalletFactory $partnerWalletFactory)
    {
        $this->auth = $auth;
        $this->partnerWalletFactory = $partnerWalletFactory;
    }

    public function personWallets(): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();
        $wallets = $this->partnerWalletFactory->factoryWallets($user);

        return $this->responseJsonCollection($wallets);
    }

    public function userWallet(Partner $partner): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();
        $wallet = $this->partnerWalletFactory->factoryPartnerWallet($user, $partner);

        if (!$wallet) {
            return $this->notFound();
        }

        return $this->responseJson($wallet);
    }
}
