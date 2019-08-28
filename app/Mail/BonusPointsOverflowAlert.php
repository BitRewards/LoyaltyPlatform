<?php

namespace App\Mail;

use App\DTO\Mail\BonusesOverflowData;
use App\Mail\Base\AdministratorNotification;
use App\Services\Alerts\BonusesOverflowAlert;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BonusPointsOverflowAlert extends AdministratorNotification
{
    /**
     * @var Collection
     */
    protected $users;

    /**
     * @var Collection
     */
    protected $balances;

    /**
     * @var string
     */
    protected $period;

    /**
     * @var int
     */
    protected $periodLimit;

    /**
     * @var Carbon
     */
    protected $start;

    /**
     * @var Carbon
     */
    protected $end;

    /**
     * BonusPointsOverflowAlert constructor.
     *
     * @param BonusesOverflowData $data
     */
    public function __construct(BonusesOverflowData $data)
    {
        parent::__construct($data->partner->mainAdministrator);

        $this->users = $data->users;
        $this->balances = $data->balances;
        $this->period = $data->period;
        $this->periodLimit = $data->periodLimit;
        $this->start = $data->start;
        $this->end = $data->end;
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return 'emails.bonuses-overflow';
    }

    /**
     * @return array
     */
    protected function getTemplateVariables(): array
    {
        return [
            'partner' => $this->partner,
            'users' => $this->users,
            'balances' => $this->balances,
            'period' => $this->period,
            'periodLimit' => $this->periodLimit,
            'start' => $this->start,
            'end' => $this->end,
        ];
    }

    /**
     * @return string
     */
    protected function getSubject(): string
    {
        if (BonusesOverflowAlert::LIMIT_LAST_WEEK === $this->period) {
            return __('Exceeded the limit of bonuses given for the week');
        }

        return __('Exceeded daily bonus limit');
    }
}
