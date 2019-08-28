<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Reward;
use App\Models\StoreEntity;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportsService
{
    /**
     * Report cache duration in minutes.
     */
    const CACHE_TTL = 60;

    /**
     * @var array
     */
    protected $transactionsCache = [];

    /**
     * Re-generates loyalty report or retrieves cached values.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return array
     */
    public function generate(Partner $partner, Carbon $from, Carbon $till)
    {
        $reportStack = collect([]);

        return [
            'period' => [
                'from' => $fullFromDate = \HDate::dateFull($from->timestamp),
                'till' => $fullTillDate = \HDate::dateFull($till->timestamp),
                'title' => __('Report for the %s - %s period', $fullFromDate, $fullTillDate),
            ],
            'users' => [
                'start_count' => $this->usersCountAt($partner, $from, $reportStack, 'users.count.start'),
                'end_count' => $this->usersCountAt($partner, $till, $reportStack, 'users.count.end'),
                'diff' => $reportStack->get('users.count.end') - $reportStack->get('users.count.start'),
            ],
            'orders' => [
                'paid_with_bonuses' => [
                    'sum' => \HAmount::fSign(
                        $this->ordersPaidWithBonusesSum($partner, $from, $till),
                        $partner->currency
                    ),
                    'avg' => \HAmount::fSign(
                        $this->ordersPaidWithBonusesAverage($partner, $from, $till),
                        $partner->currency
                    ),
                ],
                'sum' => \HAmount::fSign($this->ordersSum($partner, $from, $till), $partner->currency),
                'avg' => \HAmount::fSign($this->ordersAvearage($partner, $from, $till), $partner->currency),
            ],
            'bonuses' => [
                'given' => $this->givenBonusPoints($partner, $from, $till),
                'taken' => $this->takenBonusPoints($partner, $from, $till),
            ],
            'popular_rewards' => $this->popularRewards($partner, $from, $till),
            'popular_actions' => $this->popularActions($partner, $from, $till),
            'active_users' => $this->activeUsers($partner, $from, $till),
            'most_earned_users' => $this->mostEarnedUsers($partner, $from, $till),
            'most_spent_users' => $this->mostSpentUsers($partner, $from, $till),
        ];
    }

    /**
     * Retrieves Partner's users count at given period.
     *
     * @param Partner    $partner
     * @param Carbon     $date
     * @param Collection $stack
     * @param string     $stackKey
     *
     * @return int
     */
    public function usersCountAt(Partner $partner, Carbon $date, Collection $stack = null, string $stackKey = null)
    {
        $count = Cache::remember(
            $this->cacheKey($partner, $date, $date, 'users-count-at-'.$date->format('Y-m-d')),
            static::CACHE_TTL,
            function () use ($partner, $date) {
                $count = User::where('partner_id', $partner->id)
                    ->where('created_at', '<', $date)
                    ->count();

                return $count;
            }
        );

        if (!is_null($stack) && !is_null($stackKey)) {
            $stack->put($stackKey, $count);
        }

        return $count;
    }

    /**
     * Return sum of orders that were paid with bonuses.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function ordersPaidWithBonusesSum(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executePaidWithBonusesRequest($partner, $from, $till, 'sum');
    }

    /**
     * Returns average order amount from orders that were paid with bonuses.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function ordersPaidWithBonusesAverage(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executePaidWithBonusesRequest($partner, $from, $till, 'avg');
    }

    /**
     * Total orders sum on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function ordersSum(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeOrdersRequest($partner, $from, $till, 'sum');
    }

    /**
     * Average order amount on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function ordersAvearage(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeOrdersRequest($partner, $from, $till, 'avg');
    }

    /**
     * Awarded bonus points on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function givenBonusPoints(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeBonusPointsRequest($partner, $from, $till);
    }

    /**
     * Spent bonus points on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return int
     */
    public function takenBonusPoints(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeBonusPointsRequest($partner, $from, $till, false);
    }

    /**
     * Popular rewards acquired on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param bool    $desc
     *
     * @return array
     */
    public function popularRewards(Partner $partner, Carbon $from, Carbon $till, bool $desc = true)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'popular-rewards-'.($desc ? 'desc' : 'asc')),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $desc) {
                $result = DB::select(
                    'select r.id, r.title, r.type, count(*) as acquisitions_count, abs(sum(balance_change)) as total_amount from rewards as r '.
                    'inner join transactions as t on (r.id = t.reward_id) '.
                    'where r.partner_id = ? and t.status = ? '.
                    'and (t.created_at between ? and ?) '.
                    'group by r.id order by acquisitions_count '.($desc ? 'desc' : 'asc'),
                    [$partner->id, Transaction::STATUS_CONFIRMED, $from, $till]
                );

                $rewards = collect($result);
                $models = Reward::with('partner')
                    ->whereIn('id', $rewards->pluck('id')->toArray())
                    ->get()
                    ->keyBy('id');

                return $rewards->map(function ($reward) use ($models) {
                    $reward->title = \HReward::getTitle($models->get($reward->id));

                    return $reward;
                })->toArray();
            }
        );
    }

    /**
     * Popular actions completed on given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param bool    $desc
     *
     * @return array
     */
    public function popularActions(Partner $partner, Carbon $from, Carbon $till, bool $desc = true)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'popular-actions-'.($desc ? 'desc' : 'asc')),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $desc) {
                $result = DB::select(
                    'select a.id, a.title, a.type, count(*) as completions_count, abs(sum(balance_change)) as total_amount from actions as a '.
                    'inner join transactions as t on (a.id = t.action_id) '.
                    'where a.partner_id = ? and t.status = ? '.
                    'and (t.created_at between ? and ?) '.
                    'group by a.id order by completions_count '.($desc ? 'desc' : 'asc'),
                    [$partner->id, Transaction::STATUS_CONFIRMED, $from, $till]
                );

                return collect($result)
                    ->map(function ($action) {
                        $action->title = \HAction::getTitle($action);

                        return $action;
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Most active users for given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return array
     */
    public function activeUsers(Partner $partner, Carbon $from, Carbon $till)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'active-users'),
            static::CACHE_TTL,
            function () use ($partner, $from, $till) {
                $result = DB::select(
                    'select u.id, u.name, u.email, u.phone, count(t.*) as transactions_count from users as u '.
                    'inner join transactions as t on (u.id = t.user_id) '.
                    'where u.partner_id = ? and t.status = ? '.
                    'and (t.created_at between ? and ?) '.
                    'group by u.id order by transactions_count desc '.
                    'limit 10',
                    [$partner->id, Transaction::STATUS_CONFIRMED, $from, $till]
                );

                return $result;
            }
        );
    }

    /**
     * Most richest users for given period.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return array
     */
    public function richestUsers(Partner $partner, Carbon $from, Carbon $till)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'richest-users'),
            static::CACHE_TTL,
            function () use ($partner) {
                $result = DB::select(
                    'select id, name, email, phone, balance from users '.
                    'where partner_id = ? '.
                    'order by balance desc '.
                    'limit 10',
                    [$partner->id]
                );

                return collect($result)->map(function ($user) {
                    $user->balance = intval($user->balance);

                    return $user;
                })->toArray();
            }
        );
    }

    /**
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return Collection
     */
    public function mostEarnedUsers(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeUsersPointsChangesRequest($partner, $from, $till);
    }

    /**
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return Collection
     */
    public function mostSpentUsers(Partner $partner, Carbon $from, Carbon $till)
    {
        return $this->executeUsersPointsChangesRequest($partner, $from, $till, false);
    }

    /**
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param bool    $earned
     *
     * @return Collection
     */
    protected function executeUsersPointsChangesRequest(Partner $partner, Carbon $from, Carbon $till, bool $earned = true)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'user-points-change-'.($earned ? 'earned' : 'spent')),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $earned) {
                $pointsSum = ($earned ? '' : '-1 * ').'sum(t.balance_change)';

                $result = DB::select(
                    'select u.id, u.name, u.email, u.phone, '.$pointsSum.' as points from users as u '.
                    'inner join transactions as t on (u.id = t.user_id) '.
                    'where u.partner_id = ? and t.status = ? and t.balance_change '.($earned ? '>' : '<').' 0'.
                    'and (t.created_at between ? and ?) '.
                    'group by u.id order by points desc '.
                    'limit 10',
                    [$partner->id, Transaction::STATUS_CONFIRMED, $from, $till]
                );

                return $result;
            }
        );
    }

    /**
     * Retrieves orders that were paid with bonuses and applies given aggregate function.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param string  $func
     *
     * @return int
     */
    protected function executePaidWithBonusesRequest(Partner $partner, Carbon $from, Carbon $till, string $func)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'orders-paid-with-bonuses-'.$func),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $func) {
                if (!in_array($func, ['sum', 'avg'])) {
                    throw new \InvalidArgumentException('Given function name is invalid. Allowed values are: sum or avg.');
                }

                $promoCodes = $this->preparePromoCodes($partner, $from, $till);

                if (!count($promoCodes)) {
                    return 0;
                }

                $result = DB::table('store_entities')
                    ->select(DB::raw($func."((nullif(data->>'amountTotal', '0'))::float) AS amount"))
                    ->where('type', StoreEntity::TYPE_ORDER)
                    ->where('status', StoreEntity::STATUS_CONFIRMED)
                    ->where('partner_id', $partner->id)
                    ->whereBetween('created_at', [$from, $till])
                    ->whereRaw($this->buildPromoCodesWhereClause($promoCodes))
                    ->get()
                    ->first();

                if (is_null($result)) {
                    return 0;
                }

                return intval($result->amount);
            }
        );
    }

    /**
     * Retrieves orders for given period and applies given aggregate function.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param string  $func
     *
     * @return int
     */
    protected function executeOrdersRequest(Partner $partner, Carbon $from, Carbon $till, string $func)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'orders-'.$func),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $func) {
                if (!in_array($func, ['sum', 'avg'])) {
                    throw new \InvalidArgumentException('Given function name is invalid. Allowed values are: sum or avg.');
                }

                $result = DB::table('store_entities')
                    ->select(DB::raw($func."((nullif(data->>'amountTotal', '0'))::float) AS amount"))
                    ->where('type', StoreEntity::TYPE_ORDER)
                    ->where('status', StoreEntity::STATUS_CONFIRMED)
                    ->where('partner_id', $partner->id)
                    ->whereBetween('created_at', [$from, $till])
                    ->get()
                    ->first();

                if (is_null($result)) {
                    return 0;
                }

                return intval($result->amount);
            }
        );
    }

    /**
     * Retrieves given or taken bonus points sum.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param bool    $given
     *
     * @return int
     */
    protected function executeBonusPointsRequest(Partner $partner, Carbon $from, Carbon $till, bool $given = true)
    {
        return Cache::remember(
            $this->cacheKey($partner, $from, $till, 'bonus-points-'.($given ? 'given' : 'taken')),
            static::CACHE_TTL,
            function () use ($partner, $from, $till, $given) {
                $result = DB::table('transactions')
                    ->where('partner_id', $partner->id)
                    ->where('status', Transaction::STATUS_CONFIRMED)
                    ->where('partner_id', $partner->id)
                    ->whereBetween('created_at', [$from, $till])
                    ->where('balance_change', ($given ? '>' : '<'), 0)
                    ->sum('balance_change');

                return abs(intval($result));
            }
        );
    }

    /**
     * @param Collection $promoCodes
     *
     * @return string
     */
    protected function buildPromoCodesWhereClause(Collection $promoCodes)
    {
        $promoCodesStr = $promoCodes->map(function ($code) {
            return "'".$code."'";
        })->implode(', ');

        return collect([0, 1, 2])->map(function ($index) use ($promoCodesStr) {
            return "data->'promoCodes'->>".$index.' IN ('.$promoCodesStr.')';
        })->implode(' or ');
    }

    /**
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return mixed
     */
    protected function confirmedTransactions(Partner $partner, Carbon $from, Carbon $till)
    {
        if (!empty($this->transactionsCache[$partner->id])) {
            return $this->transactionsCache[$partner->id];
        }

        return $this->transactionsCache[$partner->id] = DB::table('transactions')
            ->select(DB::raw("data->>'promo_code' AS promo_code"))
            ->where('partner_id', $partner->id)
            ->where('status', Transaction::STATUS_CONFIRMED)
            ->whereBetween('created_at', [$from, $till])
            ->whereNotNull('reward_id')
            ->whereRaw("data->>'promo_code' IS NOT NULL")
            ->pluck('promo_code')
            ->reject(null);
    }

    /**
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     *
     * @return Collection
     */
    protected function preparePromoCodes(Partner $partner, Carbon $from, Carbon $till)
    {
        $promoCodes = $this->confirmedTransactions($partner, $from, $till);

        if (!count($promoCodes)) {
            return collect([]);
        }

        return $promoCodes;
    }

    /**
     * Generates cache key for given data.
     *
     * @param Partner $partner
     * @param Carbon  $from
     * @param Carbon  $till
     * @param string  $suffix
     *
     * @return string
     */
    protected function cacheKey(Partner $partner, Carbon $from, Carbon $till, string $suffix)
    {
        return implode('-', [
            'partner-report',
            'partner', $partner->id,
            'from', $from->format('Y-m-d'),
            'till', $till->format('Y-m-d'),
            $suffix,
        ]);
    }
}
