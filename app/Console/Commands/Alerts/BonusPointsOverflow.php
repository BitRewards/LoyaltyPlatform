<?php

namespace App\Console\Commands\Alerts;

use App\Mail\BonusPointsOverflowAlert;
use App\Services\Alerts\BonusesOverflowAlert;
use Illuminate\Console\Command;

class BonusPointsOverflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:bonuses-overflow {period=yesterday} {lang=ru}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends bonus points overflow alerts to Partners.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->argument('period');
        $lang = $this->argument('lang');
        $this->comment('Using peiod "'.$period.'" and lang "'.$lang.'".');

        $currentLang = \HLanguage::getCurrent();
        \HLanguage::setLanguage($lang);

        $alert = new BonusesOverflowAlert($period, $lang);
        $emails = $alert->emails();

        if (!count($emails)) {
            $this->comment('No emails are required to be sent.');

            return;
        }

        $this->comment('Got '.count($emails).' emails to be sent.');

        $delay = 30;
        $emails->each(function (BonusPointsOverflowAlert $mailable) use (&$delay) {
            $this->comment('Mail will be sent in '.$delay.' seconds.');
            \Mail::later($delay, $mailable);
            $delay += 10;
        });

        \HLanguage::setLanguage($currentLang);

        $this->info(count($emails).' were queued to be sent.');
    }
}
