<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\DepositHistoryFactory;
use App\DTO\Factory\TransactionHistoryFactory;
use App\DTO\Factory\WithdrawHistoryFactory;
use App\Http\Controllers\ClientApiController;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TransactionController extends ClientApiController
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var TransactionHistoryFactory
     */
    private $transactionHistoryFactory;

    /**
     * @var WithdrawHistoryFactory
     */
    private $withdrawHistoryFactory;

    /**
     * @var DepositHistoryFactory
     */
    private $depositHistoryFactory;

    public function __construct(
        Auth $auth,
        TransactionService $transactionService,
        TransactionHistoryFactory $transactionHistoryFactory,
        DepositHistoryFactory $depositHistoryFactory,
        WithdrawHistoryFactory $withdrawHistoryFactory
    ) {
        $this->auth = $auth;
        $this->transactionService = $transactionService;
        $this->transactionHistoryFactory = $transactionHistoryFactory;
        $this->withdrawHistoryFactory = $withdrawHistoryFactory;
        $this->depositHistoryFactory = $depositHistoryFactory;
    }

    public function personTransactionList(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();

        $transactions = $this
            ->transactionService
            ->getPersonTransactions($user, ...$this->getPageParameters($request));

        return $this->buildResponse(
            $transactions,
            $this->transactionService->getPersonTransactionsCount($user)
        );
    }

    public function userTransactionList(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();
        $transactions = $this
            ->transactionService
            ->getUserTransactions($user, ...$this->getPageParameters($request));

        return $this->buildResponse(
            $transactions,
            $this->transactionService->getUserTransactionsCount($user)
        );
    }

    protected function buildResponse(Collection $transactions, int $totalCount): JsonResponse
    {
        $transactionHistory = $this->transactionHistoryFactory->factoryCollection($transactions);

        return $this->responseJsonCollection(
            $transactionHistory,
            $totalCount
        );
    }

    public function withdrawTransactionList(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->auth::user();
        $pageParams = $this->getPageParameters($request);

        $transactions = $this->transactionService->getWithdrawTransactions($currentUser, ...$pageParams);
        $withdrawTransactions = $this->withdrawHistoryFactory->factoryCollection($transactions);

        return $this->responseJsonCollection($withdrawTransactions);
    }

    public function depositHistory(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->auth::user();

        $transactions = $this
            ->transactionService
            ->getDepositTransactions($user, ...$this->getPageParameters($request));

        $depositHistory = $this
            ->depositHistoryFactory
            ->factoryCollection($transactions);

        return $this->responseJsonCollection(
            $depositHistory,
            $this->transactionService->getDepositTransactionsCount($user)
        );
    }
}
