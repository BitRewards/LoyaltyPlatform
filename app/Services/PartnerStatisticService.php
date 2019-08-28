<?php

namespace App\Services;

use App\DTO\PartnerStatistic\LoyaltyPurchaseStatisticData;
use App\DTO\PartnerStatistic\ReferralPurchaseStatisticData;
use App\Models\Action;
use App\Models\Partner;
use App\Models\StoreEntity;
use GL\Rabbit\DTO\RPC\CRM\PartnerStatistic\PurchaseTrendData;
use GL\Rabbit\DTO\RPC\CRM\PartnerStatisticResponse;
use Illuminate\Database\Query\Builder;

class PartnerStatisticService
{
    /**
     * @var PartnerService
     */
    protected $partnerService;

    public function __construct(PartnerService $partnerService)
    {
        $this->partnerService = $partnerService;
    }

    public function getStatistic(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): PartnerStatisticResponse {
        $from = $from ?? $partner->created_at;
        $to = $to ?? new \DateTime();

        $data = new PartnerStatisticResponse();
        $data->isReferralSystemEnabled = $partner->isFiatReferralEnabled();
        $data->totalUserCount = $partner->users()->count();
        $data->activeUsersCount = $this->getTotalUsersCount($partner, $from, $to);
        $data->totalUserCountForPeriod = $partner->users()->where('created_at', '<=', $to)->count();
        $data->registrationCount = $this->partnerService->getNewRegistrationCount($partner, $from, $to);

        $referralStatistic = $this->getReferralPurchaseStatistic($partner, $from, $to);
        $data->referralPurchaseCount = $referralStatistic->purchaseCount;
        $data->referralPurchaseAmount = $referralStatistic->purchaseTotalAmount;
        $data->referralAveragePurchaseAmount = $referralStatistic->averagePurchaseAmount;

        $loyaltyPurchaseStatistic = $this->getLoyaltyPurchaseStatistic($partner, $from, $to);
        $data->loyaltyPurchasesCount = $loyaltyPurchaseStatistic->purchaseCount;
        $data->loyaltyPurchasesAmount = $loyaltyPurchaseStatistic->purchaseTotalAmount;
        $data->loyaltyAveragePurchaseAmount = $loyaltyPurchaseStatistic->averagePurchaseAmount;
        $data->loyaltyPromoPurchaseCount = $loyaltyPurchaseStatistic->promoPurchaseCount;
        $data->loyaltyPromoPurchaseAmount = $loyaltyPurchaseStatistic->promoPurchaseAmount;
        $data->loyaltyAveragePromoPurchaseAmount = $loyaltyPurchaseStatistic->averagePromoPurchaseAmount;

        $data->averagePurchaseAmount = $this->getAveragePurchaseAmount($partner, $from, $to);
        $data->loyaltyPurchaseTrend = $this->getLoyaltyMonthlyPurchaseTrend($partner);
        $data->referralPurchaseTrend = $this->getReferralMonthlyPurchaseTrend($partner);

        $data->averageCheckAmount = $this->getAverageCheckAmount($partner, $from, $to);

        return $data;
    }

    public function loyaltyStoreEntitiesQuery(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): Builder {
        $query = \DB::query()
            ->from('transactions as t')
            ->join('store_entities as se', 'se.id', 't.source_store_entity_id')
            ->where('t.partner_id', $partner->id);

        if ($from) {
            $query->where('se.created_at', '>=', $from);
        }

        if ($to) {
            $query->where('se.created_at', '<', $to);
        }

        return $query;
    }

    public function confirmedLoyaltyStoreEntitiesQuery(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): Builder {
        return $this
            ->loyaltyStoreEntitiesQuery($partner, $from, $to)
            ->where('se.status', '!=', StoreEntity::STATUS_REJECTED)
            ->where('se.data', '@>', json_encode(['isPaid' => true]));
    }

    public function referralStoreEntitiesQuery(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): Builder {
        return $this
            ->loyaltyStoreEntitiesQuery($partner, $from, $to)
            ->join('actions as a', 't.action_id', 'a.id')
            ->where('a.type', Action::TYPE_ORDER_REFERRAL)
            ->where('a.partner_id', $partner->id);
    }

    public function confirmedReferralStoreEntitiesQuery(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): Builder {
        return $this
            ->confirmedLoyaltyStoreEntitiesQuery($partner, $from, $to)
            ->join('actions as a', 't.action_id', 'a.id')
            ->where('a.type', Action::TYPE_ORDER_REFERRAL)
            ->where('a.partner_id', $partner->id);
    }

    public function getReferralPurchaseStatistic(
        Partner $partner,
        \DateTime $from,
        \DateTime $to
    ): ReferralPurchaseStatisticData {
        /** @var \stdClass $stats */
        $stats = $this
            ->confirmedReferralStoreEntitiesQuery($partner, $from, $to)
            ->select(
                \DB::raw('COUNT(*) as count'),
                \DB::raw("COALESCE(SUM((se.data->>'amountTotal')::float), 0) as amount")
            )->first();

        $data = new ReferralPurchaseStatisticData();
        $data->purchaseCount = (int) $stats->count;
        $data->purchaseTotalAmount = (float) $stats->amount;

        if ($data->purchaseCount) {
            $data->averagePurchaseAmount = round($data->purchaseTotalAmount / $data->purchaseCount, 2);
        }

        return $data;
    }

    public function getLoyaltyPurchaseStatistic(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): LoyaltyPurchaseStatisticData {
        /** @var \stdClass $result */
        $result = $this
            ->confirmedLoyaltyStoreEntitiesQuery($partner, $from, $to)
            ->select(
                \DB::raw('COUNT(*) as count'),
                \DB::raw("COALESCE(SUM((se.data->>'amountTotal')::float), 0) as total_amount"),
                \DB::raw(
                    "SUM(CASE WHEN se.data->>'promoCodes' NOTNULL AND se.data->>'promoCodes' != '[]' THEN 1 ELSE 0 END) as promo_count"
                ),
                \DB::raw(
                    "SUM(CASE WHEN se.data->>'promoCodes' NOTNULL AND se.data->>'promoCodes' != '[]' THEN (se.data->>'amountTotal')::float ELSE 0 END) as promo_total_amount"
                )
            )->first();

        $data = new LoyaltyPurchaseStatisticData();
        $data->purchaseCount = (int) $result->count;
        $data->purchaseTotalAmount = (float) $result->total_amount;
        $data->averagePurchaseAmount = 0.;
        $data->promoPurchaseCount = (int) $result->promo_count;
        $data->promoPurchaseAmount = (float) $result->promo_total_amount;
        $data->averagePromoPurchaseAmount = 0.;

        if ($data->purchaseCount) {
            $data->averagePurchaseAmount = round($data->purchaseTotalAmount / $data->purchaseCount, 2);
        }

        if ($data->promoPurchaseCount) {
            $data->averagePromoPurchaseAmount = round(
                $data->promoPurchaseAmount / $data->promoPurchaseCount,
                2
            );
        }

        return $data;
    }

    public function getAveragePurchaseAmount(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): float {
        $query = StoreEntity::query()
            ->select(
                \DB::raw('COUNT(*)'),
                \DB::raw("COALESCE(SUM((data->>'amountTotal')::float), 0) as amount")
            )
            ->where('partner_id', $partner->id);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<', $to);
        }

        /** @var \stdClass $stat */
        $stat = $query->first();

        if ($stat->count) {
            return round(($stat->amount / $stat->count), 2);
        }

        return 0.;
    }

    protected function getMonthlyPurchaseTrendResult(
        Builder $query,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $trendData = $query
            ->select(
                \DB::raw('count(*) as count'),
                \DB::raw("SUM((se.data->>'amountTotal')::FLOAT) as amount"),
                \DB::raw("to_char(se.created_at, 'yyyy-mm') as date")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->all();

        $trendData = array_map(
            static function (\stdClass $std) {
                return [
                    'purchaseCount' => (int) $std->count,
                    'purchaseTotalAmount' => (float) $std->amount,
                    'averagePurchaseAmount' => round($std->amount / $std->count),
                    'date' => $std->date,
                ];
            },
            $trendData
        );

        return $this->normalizeMonthlyTrendData($trendData, $from, $to);
    }

    protected function getDailyPurchaseTrendResult(
        Builder $query,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $trendData = $query
            ->select(
                \DB::raw('count(*) as count'),
                \DB::raw("SUM((se.data->>'amountTotal')::FLOAT) as amount"),
                \DB::raw("to_char(se.created_at, 'yyyy-mm-dd') as date")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->all();

        $trendData = array_map(
            static function (\stdClass $std) {
                return [
                    'purchaseCount' => (int) $std->count,
                    'purchaseTotalAmount' => (float) $std->amount,
                    'averagePurchaseAmount' => round($std->amount / $std->count),
                    'date' => $std->date,
                ];
            },
            $trendData
        );

        return $this->normalizeDailyTrendData($trendData, $from, $to);
    }

    /**
     * @param array          $trendData
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function normalizeMonthlyTrendData(
        array $trendData,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        if (empty($trendData) && null === $from) {
            return [];
        }

        $trendDataMap = [];

        foreach ($trendData as $item) {
            $trendDataMap[$item['date']] = $item;
        }

        $from = $from ?? new \DateTime(reset($trendData)['date']);
        $to = $to ?? new \DateTime();
        $period = new \DatePeriod($from, new \DateInterval('P1M'), $to);
        $normalizedData = [];

        foreach ($period as $periodDate) {
            $date = $periodDate->format('Y-m');
            $normalizedData[] = $trendData = new PurchaseTrendData();
            $trendData->date = $periodDate;
            $trendData->purchaseCount = $trendDataMap[$date]['purchaseCount'] ?? 0;
            $trendData->purchaseTotalAmount = $trendDataMap[$date]['purchaseTotalAmount'] ?? 0.;
            $trendData->averagePurchaseAmount = $trendDataMap[$date]['averagePurchaseAmount'] ?? 0.;
        }

        return $normalizedData;
    }

    protected function normalizeDailyTrendData(
        array $trendData,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        if (empty($trendData) && null === $from) {
            return [];
        }

        $trendDataMap = [];

        foreach ($trendData as $item) {
            $trendDataMap[$item['date']] = $item;
        }

        $from = $from ?? new \DateTime(reset($trendData)['date']);
        $to = $to ?? new \DateTime();
        $period = new \DatePeriod($from, new \DateInterval('P1D'), $to);
        $normalizedData = [];

        foreach ($period as $periodDate) {
            $date = $periodDate->format('Y-m-d');
            $normalizedData[] = $trendData = new PurchaseTrendData();
            $trendData->date = $periodDate;
            $trendData->purchaseCount = $trendDataMap[$date]['purchaseCount'] ?? 0;
            $trendData->purchaseTotalAmount = $trendDataMap[$date]['purchaseTotalAmount'] ?? 0.;
            $trendData->averagePurchaseAmount = $trendDataMap[$date]['averagePurchaseAmount'] ?? 0.;
        }

        return $normalizedData;
    }

    public function getLoyaltyDailyPurchaseTrend(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $query = $this->confirmedLoyaltyStoreEntitiesQuery($partner, $from, $to);

        return $this->getDailyPurchaseTrendResult($query);
    }

    public function getLoyaltyMonthlyPurchaseTrend(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $query = $this->confirmedLoyaltyStoreEntitiesQuery($partner, $from, $to);

        return $this->getMonthlyPurchaseTrendResult($query);
    }

    public function getReferralMonthlyPurchaseTrend(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $query = $this->confirmedReferralStoreEntitiesQuery($partner, $from, $to);

        return $this->getMonthlyPurchaseTrendResult($query, $from, $to);
    }

    public function getReferralDailyPurchaseTrend(
        Partner $partner,
        ?\DateTime $from = null,
        ?\DateTime $to = null
    ): array {
        $query = $this->confirmedReferralStoreEntitiesQuery($partner, $from, $to);

        return $this->getDailyPurchaseTrendResult($query, $from, $to);
    }

    public function getTotalUsersCount(
        Partner $partner,
        \DateTime $from,
        \DateTime $to
    ): int {
        return $partner->users()
//            ->whereBetween('last_active_at', [$from, $to])
            ->whereIn('id', function ($query) use ($from, $to) {
                $query->selectRaw('DISTINCT user_id')
                    ->from('transactions')
                    ->whereBetween('created_at', [$from, $to]);
            })
            ->count();
    }

    public function getAverageCheckAmount(
        Partner $partner,
        \DateTime $from,
        \DateTime $to
    ): int {
        $data = $partner->storeEntities()
            ->whereNotNull('confirmed_at')
            ->where('type', StoreEntity::TYPE_ORDER)
            ->whereBetween('created_at', [$from, $to])
            ->select(
                \DB::raw("avg((data->>'amountTotal')::FLOAT) as amount")
            )->first();

        return $data->amount ?? 0;
    }
}
