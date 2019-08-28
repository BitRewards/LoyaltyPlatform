<?php

namespace App\Jobs;

use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class GiveMassAward implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $partner_id;
    protected $points;
    protected $comment;
    protected $onlyConfirmedEmails;

    public function __construct($partner_id, $points, $comment = '', $onlyConfirmedEmails = true)
    {
        $this->partner_id = $partner_id;
        $this->points = $points;
        $this->comment = $comment;
        $this->onlyConfirmedEmails = $onlyConfirmedEmails;
    }

    public function handle()
    {
        $partner = Partner::whereId($this->partner_id)->first();

        if (!$partner || !($this->points > 0)) {
            return;
        }

        if ($partner->getSetting(Partner::SETTINGS_IS_ALL_NOTIFICATIONS_DISABLED)) {
            return;
        }

        app(PartnerService::class)->giveCustomBonusToAllUsers($partner, $this->points, $this->comment, $this->onlyConfirmedEmails);
    }
}
