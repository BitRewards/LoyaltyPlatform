<?php

namespace App\Console;

use App\Console\Commands\Admitad\RefreshAccessToken;
use App\Console\Commands\Admitad\SyncEvents;
use App\Console\Commands\AutoFinishStoreEntitiesStatus;
use App\Console\Commands\FiatRatesUpdate;
use App\Console\Commands\Localization\UpdateFromCsv;
use App\Console\Commands\Monitoring\ExpirationTransactionMonitoring;
use App\Console\Commands\Monitoring\StoreEventMonitoring;
use App\Console\Commands\Partners\AverageChequeIncreaseCache;
use App\Console\Commands\ProcessEvents;
use App\Console\Commands\User\SendBurningPointsSummary;
use App\Console\Commands\User\UpdateReferralLinks;
use App\Services\Alerts\BonusesOverflowAlert;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    private $enableAdmitad = false;

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        if (time() < strtotime('1 dec 2016')) {
            $schedule->command(AutoFinishStoreEntitiesStatus::class)->withoutOverlapping()->everyMinute();
        } else {
            $schedule->command(AutoFinishStoreEntitiesStatus::class)->withoutOverlapping()->hourly();
        }

        $schedule->command(ProcessEvents::class)->withoutOverlapping()->everyMinute();
        // $schedule->command(Email::class)->withoutOverlapping()->everyFiveMinutes();
        $schedule->command(Commands\Backup\Db::class)->withoutOverlapping()->dailyAt('4:00');

        $moscowMidnight = Carbon::now('Europe/Moscow')->endOfDay();
        $losAngelesMidnight = Carbon::now('America/Los_Angeles')->endOfDay();

        foreach (['ru' => $moscowMidnight, 'en' => $losAngelesMidnight] as $lang => $midnight) {
            $schedule->command('alerts:bonuses-overflow', [BonusesOverflowAlert::PERIOD_YESTERDAY, $lang])
                ->withoutOverlapping()
                ->daily()
                ->at($midnight->hour(11)->copy()->setTimezone('UTC')->hour.':00');

            $schedule->command('alerts:bonuses-overflow', [BonusesOverflowAlert::PERIOD_LAST_WEEK, $lang])
                ->withoutOverlapping()
                ->mondays()
                ->at($midnight->hour(11)->copy()->setTimezone('UTC')->hour.':00');
        }

        if (\HApp::isProduction()) {
            $schedule->command(FiatRatesUpdate::class)->withoutOverlapping()->cron('0 */12 * * *');
        } else {
            $schedule->command(FiatRatesUpdate::class)->withoutOverlapping()->cron('0 0 */10 * *');
        }

        if (\HApp::isProduction()) {
            $schedule->command(StoreEventMonitoring::class)->withoutOverlapping()->cron('0 */1 * * *');
        }

        $schedule->command(UpdateReferralLinks::class)->withoutOverlapping()->cron('*/10 * * * *');
        $schedule->command(UpdateFromCsv::class)->withoutOverlapping()->cron('0 */10 * * *');

        if ($this->enableAdmitad) {
            $schedule->command(RefreshAccessToken::class)->withoutOverlapping()->cron('0 * * * *');
            $schedule->command(SyncEvents::class)->withoutOverlapping()->cron('*/10 * * * *');
        }

        $schedule->command(ExpirationTransactionMonitoring::class)->withoutOverlapping()->cron('*/5 * * * *');
        $schedule->command(SendBurningPointsSummary::class)->withoutOverlapping()->everyMinute();
        $schedule->command(AverageChequeIncreaseCache::class)->withoutOverlapping()->cron('0 4 * * *');
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
