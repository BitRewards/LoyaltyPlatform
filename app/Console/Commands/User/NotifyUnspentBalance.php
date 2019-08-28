<?php

namespace App\Console\Commands\User;

use App\Models\Notification;
use App\Models\Partner;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PartnerService;
use App\Settings\PartnerSettings;
use Illuminate\Console\Command;
use App\Mail\PositiveBalance;

class NotifyUnspentBalance extends Command
{
    private $_isTestMode = false;
    private $_isFakeMode = false;

    const CHUNK_SIZE = 100;

    const MODE_TEST = 'test';
    const MODE_PROD = 'prod';
    const MODE_FAKE = 'fake';

    const FAKE_JSON_PATH = '/resources/fake_data.json';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:notify-unspent-balance {mode='.self::MODE_TEST.'} {fakeDate=now} {limit=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify all partners with positive balances';

    private function _getPredefinedFakeUserData($limit = 10)
    {
        $users = json_decode(file_get_contents(base_path().self::FAKE_JSON_PATH));

        $fakeUsers = [];

        foreach ($users as $k => $user) {
            if ($k + 1 > $limit) {
                continue;
            }
            $fakeUsers[] = new FakeUser($user);
        }

        return $fakeUsers;
    }

    private function _process($users, $time = null)
    {
        $processTime = $time ?? time();

        $this->info('⏰ Current time is '.date('d.m.Y H:i:s', $processTime));

        foreach ($users as $user) {
            $this->info("\n");
            $lastTransactionDate = $user->getLastTransactionDate();

            // Skip users with no transactions:
            if (!$lastTransactionDate) {
                continue;
            }

            $daysSinceLastTransaction = ($processTime - $lastTransactionDate->getTimestamp()) / (3600 * 24);

            $firstReminderPeriod = \HCustomizations::setting($user->partner, PartnerSettings::UNSPENT_BALANCE_REMINDER_PERIOD_FIRST);
            $secondReminderPeriod = \HCustomizations::setting($user->partner, PartnerSettings::UNSPENT_BALANCE_REMINDER_PERIOD_SECOND);

            $this->info("User # {$user->id} ({$user->name} / {$user->email}). Partner: {$user->partner->title}");
            $this->info("Last transaction date: {$lastTransactionDate->format('d.m.Y')}");
            $this->info('Days since last transaction: '.round($daysSinceLastTransaction, 1));
            $this->info("Reminders period: {$firstReminderPeriod}, {$secondReminderPeriod}");

            if (!$this->_isFakeMode) {
                $firstNotification = Notification::model()->findByUserAndType($user, Notification::TYPE_UNSPENT_BALANCE_REMINDER_FIRST);
                $secondNotification = Notification::model()->findByUserAndType($user, Notification::TYPE_UNSPENT_BALANCE_REMINDER_SECOND);
            } else {
                $firstNotification = $user->firstNotification;
                $secondNotification = $user->secondNotification;
            }

            $daysSinceFirstNotification = $firstNotification ? ($processTime - $firstNotification->created_at->getTimestamp()) / (3600 * 24) : 0;
            $daysSinceSecondNotification = $secondNotification ? ($processTime - $secondNotification->created_at->getTimestamp()) / (3600 * 24) : 0;

            $this->info('First notification has been sent '.round($daysSinceFirstNotification, 2).' days ago');
            $this->info('Second notification has been sent '.round($daysSinceSecondNotification, 2).' days ago');

            if (!empty($firstReminderPeriod)) {
                if ($daysSinceLastTransaction > $firstReminderPeriod) {
                    // check if notification hasn't been sent yet or user was inactive afterwards
                    if (!$daysSinceFirstNotification || ($daysSinceFirstNotification > $firstReminderPeriod && $daysSinceFirstNotification > $daysSinceLastTransaction)) {
                        $this->info("✉️ Sending 1st notification to user #{$user->id}");
                        $this->sendNotification($user, Notification::TYPE_UNSPENT_BALANCE_REMINDER_FIRST);

                        continue;
                    }
                }
            }

            if (!empty($secondReminderPeriod)) {
                if ($daysSinceLastTransaction > $secondReminderPeriod) {
                    // check if notification hasn't been sent yet or user was inactive afterwards
                    if (!$daysSinceSecondNotification || ($daysSinceSecondNotification > $secondReminderPeriod && $daysSinceSecondNotification > $daysSinceLastTransaction)) {
                        $this->info("✉️   Sending 2nd notification to user #{$user->id}");
                        $this->sendNotification($user, Notification::TYPE_UNSPENT_BALANCE_REMINDER_SECOND);
                    }
                }
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @param PartnerService $partnerService
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_isTestMode = self::MODE_TEST == $this->argument('mode');
        $this->_isFakeMode = self::MODE_FAKE == $this->argument('mode');

        if ($this->_isTestMode) {
            $this->alert('Running in test mode! Users will not be notified');
        }

        if ($this->_isFakeMode) {
            $this->alert('Running in FAKE mode! Data is located in '.self::FAKE_JSON_PATH);

            $users = $this->_getPredefinedFakeUserData($this->argument('limit'));
            $this->_process($users, date_create($this->argument('fakeDate'))->getTimestamp());

            exit(0);
        }

        $this->info('Running '.__CLASS__.'. Chunk size = '.self::CHUNK_SIZE."\n");

        // find users with positive balance only:
        User::with('notifications')
            ->where('balance', '>', 0)
            ->chunk(self::CHUNK_SIZE, function ($users) {
                $this->_process($users);
            });
    }

    private function sendNotification($user, $type)
    {
        if ($this->_isTestMode || $this->_isFakeMode) {
            return;
        }

        $email = new PositiveBalance($user);

        $notification = app(NotificationService::class)->create($user, $type, $email->render());

        if ($notification) {
            \Mail::send($email);
        }
    }
}

// Не выносил эту модель отдельно, чтобы потом выпилить
class FakeUser
{
    public $id;
    public $name;
    public $email;

    public $firstNotification;
    public $secondNotification;
    public $lastTransactionDate;
    public $partner;

    public function __construct($user)
    {
        $this->id = $user->id;
        $this->email = $user->email;
        $this->name = $user->name;
        $this->lastTransactionDate = new \DateTime($user->lastTransactionDate->date);
        $this->firstNotification = new \stdClass();
        $this->secondNotification = new \stdClass();
        $this->firstNotification->created_at = new \DateTime($user->firstNotification->date);
        $this->secondNotification->created_at = new \DateTime($user->secondNotification->date);
        $this->partner = Partner::find(360);
    }

    public function getLastTransactionDate()
    {
        return $this->lastTransactionDate;
    }
}
