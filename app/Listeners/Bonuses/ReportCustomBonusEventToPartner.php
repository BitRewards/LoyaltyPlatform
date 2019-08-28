<?php

namespace App\Listeners\Bonuses;

use App\Events\Bonuses\CustomBonusGiven;
use App\Mail\CustomBonusGivenReport;

class ReportCustomBonusEventToPartner
{
    /**
     * Handle the event.
     *
     * @param CustomBonusGiven $event
     */
    public function handle(CustomBonusGiven $event)
    {
        if ($event->partner->isCustomBonusEmailDisabled()) {
            return;
        }

        if ('testing' === config('app.env')) {
            return;
        }

        $data = $event->bonusData;
        $mail = new CustomBonusGivenReport(
            $event->partner, $data->receiver, $data->bonus, $data->comment, $data->actor
        );

        \Mail::later(40, $mail);
    }
}
