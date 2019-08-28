<?php

namespace App\DTO\Mail;

use App\DTO\DTO;

class BonusesOverflowData extends DTO
{
    /**
     * @var \App\Models\Partner
     */
    public $partner;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $users;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $balances;

    /**
     * @var string
     */
    public $period;

    /**
     * @var int
     */
    public $periodLimit;

    /**
     * @var \Carbon\Carbon
     */
    public $start;

    /**
     * @var \Carbon\Carbon
     */
    public $end;
}
