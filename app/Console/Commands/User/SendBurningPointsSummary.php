<?php

namespace App\Console\Commands\User;

use App\Mail\BurningPointsSummary;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class SendBurningPointsSummary extends Command
{
    protected $signature = 'user:sendBurningPointsSummary 
                            {--w|defaultWeekday=2 : Default weekday in schedule when emails will sent} 
                            {--u|defaultUTCTime=12:00:00 : Default sent time for UTC timezone } 
                            {--m|defaultMSKTime=10:30:00 : Default sent time for MSK timezone } 
                            {--i|intervalInDays=14 : The interval in days for which the summary will be collected }';

    protected $description = 'Send burning points summary by partner schedule';

    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * @var \Mail
     */
    protected $mail;

    public function __construct(
        UserService $userService,
        TransactionService $transactionService,
        NotificationService $notificationService,
        \Mail $mail
    ) {
        parent::__construct();

        $this->userService = $userService;
        $this->transactionService = $transactionService;
        $this->notificationService = $notificationService;
        $this->mail = $mail;
    }

    public function handle(): void
    {
        $this
            ->userService
            ->getUsersForBurningPointsSummaryNotification(
                $this->option('defaultWeekday'),
                $this->option('defaultUTCTime'),
                $this->option('defaultMSKTime')
            )
            ->each(function (User $user) {
                $this->sendNotify($user);
            });
    }

    protected function sendNotify(User $user): void
    {
        $transactions = $this
            ->transactionService
            ->getTransactionsForBurningPointsSummary($user, $this->option('intervalInDays'));

        $email = new BurningPointsSummary($user, $transactions);

        $this->notificationService->create($user, Notification::TYPE_BURNING_POINTS_SUMMARY, $email->render());

        $this->output->write("Send email to {$user->email}", false, OutputInterface::VERBOSITY_VERBOSE);
        $this->mail::send($email);
        $this->line(' OK', 'info', OutputInterface::VERBOSITY_VERBOSE);
    }
}
