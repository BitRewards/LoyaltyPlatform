<?php

namespace App\DTO\Factory;

use App\DTO\PartnerWalletData;
use App\Models\Partner;
use App\Models\User;
use App\Services\Fiat\FiatService;
use App\Services\TransactionService;
use Illuminate\Support\Collection;

class PartnerWalletFactory
{
    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * @var FiatService
     */
    protected $fiatService;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    public function __construct(
        TransactionService $transactionService,
        FiatService $fiatService,
        \HAmount $amountHelper,
        PartnerFactory $partnerFactory
    ) {
        $this->transactionService = $transactionService;
        $this->fiatService = $fiatService;
        $this->amountHelper = $amountHelper;
        $this->partnerFactory = $partnerFactory;
    }

    public function factory(User $user): PartnerWalletData
    {
        $wallet = new PartnerWalletData();

        $wallet->partner = $this->partnerFactory->factory($user->partner);
        $wallet->balanceAmount = $user->balance;
        $wallet->fiatAmount = $this->fiatService->exchangeBit($user->partner->currency, $user->balance);
        $wallet->fiatCurrency = $this->amountHelper::sISO4217($user->partner->currency);
        $wallet->couponsCount = $this->transactionService->getUserDiscountTransactionsCount($user);

        return $wallet;
    }

    public function factoryWallets(User $user): Collection
    {
        return $user->getPersonUsers()->map(function (User $user) {
            return $this->factory($user);
        });
    }

    public function factoryPartnerWallet(User $user, Partner $partner): ?PartnerWalletData
    {
        foreach ($user->getPersonUsers() as $personUser) {
            if ($personUser->partner_id === $partner->id) {
                return $this->factory($personUser);
            }
        }

        return null;
    }
}
