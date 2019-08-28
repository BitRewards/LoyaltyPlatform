<?php

namespace App\Console\Commands\Partners;

use App\Models\Transaction;
use App\Models\Action;
use App\Services\PartnerService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecalculateBonuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:recalculate-bonuses {mode=test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate bonuses';

    /**
     * Execute the console command.
     *
     * @param PartnerService $partnerService
     *
     * @return mixed
     */
    public function handle()
    {
        $isProductionMode = 'prod' == $this->argument('mode');

        if ($isProductionMode) {
            if (!$this->confirm('Are you sure to run in production mode? Running this command may cause critical issues?')) {
                exit(1);
            }
        }

        $this->_recalculate($isProductionMode);
    }

    protected function _recalculate($isProductionMode = false)
    {
        $transactions = Transaction::with(['action', 'sourceStoreEntity', 'user'])
                            ->whereNotNull('action_id')
                            ->where('created_at', '>', Carbon::now()->subMonth(2))
                            ->get();

        $this->info('Found '.count($transactions).' transactions');

        $transactions->each(function (Transaction $transaction) use ($isProductionMode) {
            // $this->info("Found action #{$transaction->action->id} transaction #{$transaction->id}");

            if (!in_array($transaction->action->type, [Action::TYPE_ORDER_CASHBACK, Action::TYPE_ORDER_REFERRAL])) {
                return;
            }

            if (Action::VALUE_TYPE_PERCENT != $transaction->action->value_type) {
                return;
            }

            $amountTotal = $transaction->sourceStoreEntity->getDataProcessor()->getAmountTotal();

            $balanceChange = round(($amountTotal * $transaction->action->value / 100) * $transaction->action->partner->money_to_points_multiplier);

            if (abs($transaction->balance_change - $balanceChange) > 1) {
                $this->info("WARNING: balanceChange > 1 for transaction {$transaction->id}, action = {$transaction->action_id}. {$transaction->balance_change} ---> {$balanceChange}");
                $transaction->balance_change = $balanceChange;

                if ($isProductionMode) {
                    if ($transaction->save()) {
                        $this->info("Changing values in DB for transaction {$transaction->id}, action = {$transaction->action_id}...");
                        app(UserService::class)->recalculateBalance($transaction->user, true);
                    }
                }
            }
        });
    }
}
