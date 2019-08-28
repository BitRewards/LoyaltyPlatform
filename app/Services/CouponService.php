<?php

namespace App\Services;

use App\DTO\CouponData;
use App\DTO\Factory\CouponFactory;
use App\Models\PersonInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * @var CouponFactory
     */
    protected $couponFactory;

    public function __construct(TransactionService $transactionService, CouponFactory $couponFactory)
    {
        $this->transactionService = $transactionService;
        $this->couponFactory = $couponFactory;
    }

    /**
     * @param User $user
     * @param int  $page
     * @param int  $perPage
     *
     * @return CouponData[]|Collection
     */
    public function getCoupons(User $user, int $page = 1, int $perPage = 20): Collection
    {
        $rewards = $this->transactionService->getUserDiscountTransactions($user, $page, $perPage);

        return $this->couponFactory->factoryCollection($rewards);
    }

    public function getCouponsCount(User $user): int
    {
        return $this->transactionService->getUserDiscountTransactionsCount($user);
    }

    public function getPersonCoupons(PersonInterface $person, int $page = 1, int $perPage = 20): Collection
    {
        $rewards = $this->transactionService->getPersonDiscountTransactions($person, $page, $perPage);

        return $this->couponFactory->factoryCollection($rewards);
    }

    public function getPersonCouponsCount(PersonInterface $person): int
    {
        return $this->transactionService->getPersonDiscountTransactionsCount($person);
    }
}
