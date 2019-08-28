<?php

namespace App\Console\Commands\User;

use App\Models\Credential;
use App\Services\Persons\PersonFinder;
use Illuminate\Console\Command;

class ResetPassword extends Command
{
    protected $signature = 'user:resetPassword {email}';

    protected $description = 'Resets password of one admin/partner user by email';

    /**
     * @var PersonFinder
     */
    private $personFinderService;

    public function __construct(
        PersonFinder $personFinderService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');

        $credential = Credential::where('type_id', '=', Credential::TYPE_EMAIL)
            ->where('email', '=', $email)->first();

        if (!$credential) {
            $this->error("Error: no credential with email $email found!");

            return;
        }

        $password = str_random(10);
        $credential->setPassword($password);
        $credential->save();

        $this->info("New password for user $email: $password");
    }
}
