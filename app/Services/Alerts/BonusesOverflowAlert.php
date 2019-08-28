<?php

namespace App\Services\Alerts;

use App\DTO\Mail\BonusesOverflowData;
use App\Mail\BonusPointsOverflowAlert;
use App\Models\Partner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BonusesOverflowAlert
{
    const PERIOD_YESTERDAY = 'yesterday';
    const PERIOD_LAST_WEEK = 'last_week';
    const LIMIT_YESTERDAY = 1000;
    const LIMIT_LAST_WEEK = 2000;

    /**
     * @var string
     */
    protected $period;

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var Carbon
     */
    protected $start;

    /**
     * @var Carbon
     */
    protected $end;

    /**
     * @var int
     */
    protected $limit;

    /**
     * BonusesOverflowAlert constructor.
     *
     * @param string $period
     * @param string $lang
     */
    public function __construct(string $period, string $lang)
    {
        $this->period = $period;
        $this->lang = $lang;
        $this->start = $this->periodStart();
        $this->end = $this->periodEnd();
        $this->limit = $this->periodLimit();
    }

    /**
     * @return Collection|\App\Mail\BonusPointsOverflowAlert[]
     */
    public function emails()
    {
        $changes = $this->fetchBalanceChangesForPeriod();
        $overflows = $this->findOverflows($changes);
        $emails = collect([]);

        if (!count($overflows)) {
            return $emails;
        }

        $users = User::whereIn('id', $overflows->keys())->get();

        if (!count($users)) {
            return $emails;
        }

        $partners = Partner::with('mainAdministrator.partner')
            ->whereIn('id', $users->pluck('partner_id'))
            ->where('default_language', $this->lang)
            ->get()
            ->keyBy('id');

        $users = $users->groupBy('partner_id');

        $partners->each(function (Partner $partner) use ($users, $overflows, $emails) {
            $emails->push(
                new BonusPointsOverflowAlert($this->mailData($partner, $users, $overflows))
            );
        });

        return $emails;
    }

    /**
     * @param Partner    $partner
     * @param Collection $users
     * @param Collection $overflows
     *
     * @return \App\DTO\Mail\BonusesOverflowData
     */
    protected function mailData(Partner $partner, Collection $users, Collection $overflows)
    {
        $partnerUsers = $users->has($partner->id) ? $users->get($partner->id) : collect([]);
        $balances = collect([]);

        $partnerUsers->each(function (User $user) use ($overflows, $balances) {
            $balances->put($user->id, $overflows->get($user->id)->total_sum);
        });

        $data = [
            'partner' => $partner,
            'users' => $partnerUsers,
            'balances' => $balances,
            'period' => $this->period,
            'periodLimit' => $this->limit,
            'start' => $this->start,
            'end' => $this->end,
        ];

        return BonusesOverflowData::make($data);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function fetchBalanceChangesForPeriod()
    {
        $changes = \DB::select(
            'select user_id, sum(balance_change) as total_sum from transactions '.
            'where balance_change > 0 and (created_at between ? and ?) '.
            'group by user_id order by total_sum desc',
            [$this->start, $this->end]
        );

        return collect($changes);
    }

    /**
     * @param Collection $changes
     *
     * @return Collection
     */
    protected function findOverflows(Collection $changes)
    {
        if (!count($changes)) {
            return collect([]);
        }

        $overflows = collect([]);

        $changes->each(function (\stdClass $change) use ($overflows) {
            if (!$this->hasOverflow($change->total_sum)) {
                return;
            }

            $overflows->put($change->user_id, $change);
        });

        return $overflows;
    }

    /**
     * @param int $totalSum
     *
     * @return bool
     */
    protected function hasOverflow(int $totalSum)
    {
        return $totalSum > $this->limit;
    }

    /**
     * @return \Carbon\Carbon
     */
    protected function periodStart()
    {
        switch ($this->period) {
            case static::PERIOD_YESTERDAY:
                return Carbon::now()->subDay()->startOfDay();

            case static::PERIOD_LAST_WEEK:
                return Carbon::now()->subWeek()->startOfWeek();

            default:
                throw new \InvalidArgumentException('Given period "'.$this->period.'" is unknown.');
        }
    }

    /**
     * @return \Carbon\Carbon
     */
    protected function periodEnd()
    {
        switch ($this->period) {
            case static::PERIOD_YESTERDAY:
                return Carbon::now()->subDay()->endOfDay();

            case static::PERIOD_LAST_WEEK:
                return Carbon::now()->subWeek()->endOfWeek();

            default:
                throw new \InvalidArgumentException('Given period "'.$this->period.'" is unknown.');
        }
    }

    /**
     * @return int
     */
    protected function periodLimit()
    {
        switch ($this->period) {
            case static::PERIOD_YESTERDAY:
                return static::LIMIT_YESTERDAY;

            case static::PERIOD_LAST_WEEK:
                return static::LIMIT_LAST_WEEK;

            default:
                throw new \InvalidArgumentException('Given period "'.$this->period.'" is unknown.');
        }
    }
}
