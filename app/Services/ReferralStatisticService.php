<?php

namespace App\Services;

use App\DTO\PartnerReferrerStatisticData;
use App\DTO\Factory\FiatWithdrawTransactionFactory;
use App\DTO\ReferralStatisticData;
use App\DTO\ReferrerBalanceData;
use App\DTO\ReferrerSummaryData;
use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\StoreEntity;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Fiat\FiatService;
use App\Services\Giftd\ApiClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class ReferralStatisticService
{
    /**
     * @var ActionService
     */
    protected $actionService;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var FiatService
     */
    protected $fiatService;

    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * @var FiatWithdrawTransactionFactory
     */
    protected $fiatWithdrawTransactionFactory;

    /**
     * @var RewardService
     */
    protected $rewardService;

    /**
     * @var ApiClient
     */
    protected $giftdApiClient;

    public function __construct(
        ActionService $actionService,
        \HAmount $amountHelper,
        FiatService $fiatService,
        TransactionService $transactionService,
        FiatWithdrawTransactionFactory $fiatWithdrawTransactionFactory,
        RewardService $rewardService,
        ApiClient $giftdApiClient
    ) {
        $this->actionService = $actionService;
        $this->amountHelper = $amountHelper;
        $this->fiatService = $fiatService;
        $this->transactionService = $transactionService;
        $this->fiatWithdrawTransactionFactory = $fiatWithdrawTransactionFactory;
        $this->rewardService = $rewardService;
        $this->giftdApiClient = $giftdApiClient;
    }

    protected function getSignupBalance(User $user): float
    {
        $partner = $user->partner;
        $signupAction = $this->actionService->getSignupAction($partner);

        if (!$signupAction) {
            return 0;
        }
        $sum = Transaction::where('action_id', $signupAction->id)
                                   ->where('status', Transaction::STATUS_CONFIRMED)
                                   ->where('user_id', '=', $user->id)
                                   ->sum('balance_change');

        return (float) $sum;
    }

    protected function getReferralActionTransactionQueryBuilder(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): Builder {
        $referralAction = $this->actionService->getReferralAction($partner);

        if (!$referralAction) {
            throw new \RuntimeException('Referral action not found');
        }

        $queryBuilder = Transaction::where('action_id', $referralAction->id)
            ->where('status', Transaction::STATUS_CONFIRMED);

        if ($from && $to) {
            $queryBuilder->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $queryBuilder->where('created_at', '>=', $from);
        } elseif ($to) {
            $queryBuilder->where('created_at', '<', $to);
        }

        if ($user) {
            $queryBuilder->where('user_id', '=', $user->id);
        }

        return $queryBuilder;
    }

    protected function getStoreEntityQuery(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): QueryBuilder {
        $storeEntityIdsQuery = $this
            ->getReferralActionTransactionQueryBuilder(...\func_get_args())
            ->select('source_store_entity_id');

        return \DB::table((new StoreEntity())->getTable())
            ->whereIn('id', $storeEntityIdsQuery);
    }

    public function getReferralsPurchaseDailyStatistic(
        Partner $partner,
        \DateTime $from,
        \DateTime $to
    ): Collection {
        $purchaseStatistic = $this
            ->getStoreEntityQuery(...\func_get_args())
            ->select(
                \DB::raw('COUNT(*) as count'),
                \DB::raw("SUM(COALESCE(data ->> 'amountTotal', '0')::FLOAT) as amount"),
                \DB::raw('to_char("confirmed_at", \'YYYY-MM-DD\') as date')
            )
            ->groupBy(\DB::raw('to_char("confirmed_at", \'YYYY-MM-DD\')'))
            ->get()
            ->all();

        return $this->normalizeByDay(
            $from,
            $to,
            $purchaseStatistic,
            [
                'count' => 0,
                'amount' => 0,
            ]
        );
    }

    protected function normalizeByDay(\DateTime $from, \DateTime $to, array $data, array $default): Collection
    {
        $normalizedData = new Collection();
        $dataMap = [];

        foreach ($data as $item) {
            if ($item instanceof \stdClass) {
                $item = (array) $item;
            }

            $dataMap[$item['date']] = $item;
        }

        $from = Carbon::instance($from)->startOfDay();
        $to = Carbon::instance($to)->endOfDay();
        $datePeriod = new \DatePeriod($from, new \DateInterval('P1D'), $to);

        /** @var Carbon $item */
        foreach ($datePeriod as $item) {
            $date = $item->format('Y-m-d');
            $normalizedData->push($dataMap[$date] ?? [
                'date' => $date,
            ] + $default);
        }

        return new Collection($normalizedData);
    }

    public function getReferralsPurchasesAmountTrendData(
        Partner $partner,
        \DateTime $from,
        ?\DateTime $to = null
    ): array {
        $dailyStatistic = $this->getReferralsPurchaseDailyStatistic($partner, $from, $to ?? Carbon::now());

        return $this->toMetricFormat($dailyStatistic, 'amount');
    }

    protected function toMetricFormat(Collection $collection, string $valueColumn): array
    {
        $metricData = [];

        foreach ($collection as $item) {
            $metricData[$item['date']] = $item[$valueColumn];
        }

        return $metricData;
    }

    protected function calculateTotalPurchasesAmount(Collection $referralsPurchaseAmountDailyStatistic): float
    {
        return (float) $referralsPurchaseAmountDailyStatistic->sum('amount');
    }

    public function getReferralsPurchasesCountTrendData(
        Partner $partner,
        \DateTime $from,
        ?\DateTime $to = null
    ): array {
        $dailyStatistic = $this->getReferralsPurchaseDailyStatistic($partner, $from, $to ?? Carbon::now());

        return $this->toMetricFormat($dailyStatistic, 'count');
    }

    protected function calculateTotalPurchaseCount(Collection $referralsPurchaseAmountDailyStatistic): int
    {
        return (int) $referralsPurchaseAmountDailyStatistic->sum('count');
    }

    protected function amountFormat(User $user, $amount, bool $bold = true): string
    {
        if ($bold) {
            return $this->amountHelper::fSignBold($amount, $user->partner->currency);
        } else {
            return $this->amountHelper::fSign($amount, $user->partner->currency);
        }
    }

    public function getPurchasesAmount(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): float {
        return (float) $this
            ->getStoreEntityQuery(...\func_get_args())
            ->selectRaw("SUM(COALESCE(data->>'amountTotal', '0')::FLOAT) as totalAmount")
            ->value('totalAmount');
    }

    public function getPurchasesCount(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): int {
        return $this->getStoreEntityQuery(...\func_get_args())->count();
    }

    public function getCashBackAmount(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): float {
        $balanceChangeAmount = $this
            ->getReferralActionTransactionQueryBuilder(...\func_get_args())
            ->sum('balance_change');

        return $this->amountHelper::pointsToFiat($balanceChangeAmount, $partner);
    }

    public function getCashBackWithdrawAmount(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): float {
        $fiatWithdraw = $this->rewardService->getFiatWithdrawReward($partner);

        if (!$fiatWithdraw) {
            throw new \RuntimeException('Fiat withdraw reward not defined');
        }

        $queryBuilder = Transaction::where('reward_id', $fiatWithdraw->id)
            ->where('status', Transaction::STATUS_CONFIRMED);

        if ($from && $to) {
            $queryBuilder->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $queryBuilder->where('created_at', '>=', $from);
        } elseif ($to) {
            $queryBuilder->where('created_at', '<', $to);
        }

        if ($user) {
            $queryBuilder->where('user_id', '=', $user->id);
        }

        $withdrawAmount = $queryBuilder->sum('balance_change');

        return $this->amountHelper::pointsToFiat(abs($withdrawAmount), $user->partner);
    }

    public function getUniqueCustomersCount(Partner $partner, \DateTime $from = null, \DateTime $to = null): int
    {
        if (!$partner->getAuthMethod()) {
            throw new \RuntimeException('Unknown auth method');
        }

        return (int) $this
            ->getStoreEntityQuery(...\func_get_args())
            ->selectRaw('COUNT(DISTINCT data->>?) as count', [$partner->getAuthMethod()])
            ->value('count');
    }

    public function getReferralStatistic(
        Partner $partner,
        \DateTime $from = null,
        \DateTime $to = null,
        User $user = null
    ): ReferralStatisticData {
        $data = new ReferralStatisticData();

        $data->totalPurchasesSumAmount = $this->getPurchasesAmount(...\func_get_args());
        $data->totalPurchasesSum = $this->amountHelper::fMedium(
            $data->totalPurchasesSumAmount,
            $partner->currency
        );
        $data->purchasesCount = $this->getPurchasesCount(...\func_get_args());
        $data->cashBackAmount = $this->getCashBackAmount(...\func_get_args());
        $data->cashBack = $this->amountHelper::fMedium($data->cashBackAmount, $partner->currency);
        $data->cashBackWithdrawAmount = $this->getCashBackWithdrawAmount(...\func_get_args());
        $data->cashBackWithdraw = $this->amountHelper::fMedium(
            $data->cashBackWithdrawAmount,
            $partner->currency
        );
        $data->uniqueCustomersCount = $this->getUniqueCustomersCount(...\func_get_args());

        if ($data->purchasesCount) {
            $data->averagePurchaseAmount = $data->totalPurchasesSumAmount / $data->purchasesCount;
        } else {
            $data->averagePurchaseAmount = 0.;
        }

        $data->averagePurchase = $this->amountHelper::fMedium($data->averagePurchaseAmount, $partner->currency);
        $data->clicksCount = 0;

        if ($user->referral_link) {
            try {
                $data->clicksCount = $this->giftdApiClient::create($partner)->getClicksCountForReferralLink(
                    $user->referral_link,
                    $from,
                    $to
                );
            } catch (\Exception $e) {
                if (\HApp::isProduction()) {
                    throw $e;
                } else {
                    logger($e->getMessage(), compact($e));
                }
            }
        }

        return $data;
    }

    protected function getFiatWithdrawTransactionQueryBuilder(User $user): Builder
    {
        $fiatWithdrawReward = $this->rewardService->getFiatWithdrawReward($user->partner);

        if (!$fiatWithdrawReward) {
            throw new \RuntimeException('FiatWithdraw reward not found');
        }

        return Transaction::where('reward_id', $fiatWithdrawReward->id)
            ->where('user_id', $user->id);
    }

    public function getPaidAmount(User $user): float
    {
        $paidAmount = (float) $this
            ->getFiatWithdrawTransactionQueryBuilder($user)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->sum('balance_change');

        return $this->amountHelper::pointsToFiat(abs($paidAmount), $user->partner);
    }

    public function getBlockedAmount(User $user): float
    {
        $blockedAmount = (float) $this
            ->getFiatWithdrawTransactionQueryBuilder($user)
            ->where('status', Transaction::STATUS_PENDING)
            ->sum('balance_change');

        return $this->amountHelper::pointsToFiat(abs($blockedAmount), $user->partner);
    }

    public function getReferrerBalance(User $user): ReferrerBalanceData
    {
        $referrerBalance = new ReferrerBalanceData();

        $signupBalance = $this->getSignupBalance($user);

        $withdrawTransactions = $this->transactionService->getFiatWithdraws($user);

        $referrerBalance->earnedAmount = $this->getCashBackAmount($user->partner, null, null, $user);
        $referrerBalance->earned = $this->amountFormat($user, $referrerBalance->earnedAmount);
        $referrerBalance->blockedAmount = $this->getBlockedAmount($user);
        $referrerBalance->blocked = $this->amountFormat($user, $referrerBalance->blockedAmount);
        $referrerBalance->paidAmount = $this->getPaidAmount($user);
        $referrerBalance->paid = $this->amountFormat($user, $referrerBalance->paidAmount);
        $referrerBalance->currentBalanceAmount = $user->partner->isMergeBalancesEnabled()
            ? ($user->balance + $referrerBalance->blockedAmount)
            : ($signupBalance + $referrerBalance->earnedAmount - $referrerBalance->paidAmount);
        $referrerBalance->currentBalance = $this->amountFormat($user, $referrerBalance->currentBalanceAmount);
        $referrerBalance->availableForWithdrawAmount = $referrerBalance->currentBalanceAmount - $referrerBalance->blockedAmount;
        $referrerBalance->availableForWithdraw = $this->amountFormat($user, $referrerBalance->availableForWithdrawAmount, false);
        $referrerBalance->withdrawTransactions = $this
            ->fiatWithdrawTransactionFactory
            ->factoryCollection($withdrawTransactions);

        return $referrerBalance;
    }

    public function getUniqueReferralsCountTrendData(
        Partner $partner,
        \DateTime $from,
        \DateTime $to = null
    ): array {
        $to = $to ?? new \DateTime();

        try {
            $dailyStatistic = ApiClient::create($partner)->getUniqueReferralsCountDailyStatistic($from, $to);
        } catch (Giftd\ApiException $e) {
            $dailyStatistic = [];
        }

        $dailyStatistic = $this->normalizeByDay($from, $to, $dailyStatistic, [
            'count' => 0,
        ]);

        $result = [];

        foreach ($dailyStatistic as $item) {
            $result[$item['date']] = $item['count'];
        }

        return $result;
    }

    public function getPartnerReferralStatistic(
        Partner $partner,
        \DateTime $from,
        \DateTime $to = null
    ): PartnerReferrerStatisticData {
        $data = new PartnerReferrerStatisticData();
        $to = $to ?? new Carbon();

        $purchasesStatistic = $this->getReferralsPurchaseDailyStatistic($partner, $from, $to);

        $data->purchasesAmountDailyStatistic = $this->toMetricFormat($purchasesStatistic, 'amount');
        $data->purchasesCountDailyStatistic = $this->toMetricFormat($purchasesStatistic, 'count');
        $data->uniqueReferralsCountDailyStatistic = $this->getUniqueReferralsCountTrendData($partner, $from, $to);

        return $data;
    }

    public function referralEarningTrendData(
        Partner $partner,
        \DateTime $from,
        \DateTime $to = null,
        ?User $user = null
    ): array {
        $query = $this
            ->getReferralActionTransactionQueryBuilder(...\func_get_args())
            ->select(\DB::raw("to_char(created_at, 'yyyy-mm-dd') as date"), \DB::raw('SUM(balance_change) as amount'))
            ->groupBy('date');

        $result = \DB::select($query->toSql(), $query->getBindings());
        $result = $this->normalizeByDay($from, $to ?? Carbon::now(), $result, [
            'amount' => 0,
        ]);
        $result = $this->toMetricFormat($result, 'amount');

        return array_map(function ($amount) {
            return (float) $amount;
        }, $result);
    }

    public function referralPurchasesTrendData(User $referrer, int $dataRange): array
    {
        $from = Carbon::now()->subDay($dataRange);
        $query = Transaction::query()
            ->select(\DB::raw("to_char(transactions.created_at, 'yyyy-mm-dd') as date"), \DB::raw('COUNT(*) as count'))
            ->join('actions', 'actions.id', 'transactions.action_id')
            ->where('actions.type', Action::TYPE_ORDER_REFERRAL)
            ->where('transactions.user_id', $referrer->id)
            ->where('transactions.created_at', '>', $from)
            ->groupBy('date');

        $result = \DB::select($query->toSql(), $query->getBindings());
        $result = $this->normalizeByDay($from, Carbon::now(), $result, [
            'count' => 0,
        ]);

        return $this->toMetricFormat($result, 'count');
    }

    public function getReferrerSummary(User $referrer): ReferrerSummaryData
    {
        $data = new ReferrerSummaryData();

        $referrer
            ->transactions()
            ->with('action', 'reward')
            ->each(function (Transaction $transaction) use ($data) {
                if ($transaction->action && Action::TYPE_ORDER_REFERRAL === $transaction->action->type) {
                    ++$data->referralPurchaseCount;
                    $data->earningAmount += $transaction->balance_change;
                } elseif ($transaction->reward
                    && Reward::TYPE_FIAT_WITHDRAW === $transaction->reward->type
                    && Transaction::STATUS_CONFIRMED === $transaction->status
                ) {
                    $data->withdrawAmount = abs($transaction->balance_change);
                }
            });

        return $data;
    }
}
