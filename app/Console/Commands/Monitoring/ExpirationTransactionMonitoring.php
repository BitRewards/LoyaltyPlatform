<?php

namespace App\Console\Commands\Monitoring;

use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Console\Command;

class ExpirationTransactionMonitoring extends Command
{
    protected $signature = 'monitoring:expiration-transaction {--batch-size=10 : amount of transaction per batch }';
    protected $description = 'Monitoring expiration transaction';

    /**
     * @var TransactionService
     */
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct();

        $this->transactionService = $transactionService;
    }

    public function handle()
    {
        $batchSize = (int) $this->option('batch-size');

        do {
            $transactions = \DB::transaction(function () use ($batchSize) {
                return $this
                    ->transactionService
                    ->getRecentlyExpiredTransactions($batchSize)
                    ->map(function (Transaction $transaction) {
                        $this->transactionService->cancelExpiringPoints($transaction);

                        return $transaction;
                    });
            });
        } while ($transactions->count());
    }
}
