<?php

namespace App\Console\Commands\User;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class UpdateReferralLinks extends Command
{
    protected $signature = 'user:updateReferralLinks';

    protected $description = "Update referral links, if they doesn't exist";

    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(User $userModel, UserService $userService)
    {
        parent::__construct();

        $this->userModel = $userModel;
        $this->userService = $userService;
    }

    public function handle()
    {
        $this->userModel->whereNull('referral_link')->chunk(100, function ($users) {
            /*
             * @var User
             */
            foreach ($users as $user) {
                try {
                    $this->userService->updateReferralLink($user);
                } catch (\Throwable $e) {
                    \Log::error($e);
                    \HMisc::echoIfDebuggingInConsole($e->getMessage(), $e->getTraceAsString());
                    sleep(1);
                }
            }
        });
    }
}
