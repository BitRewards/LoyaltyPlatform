<?php

namespace App\Console\Commands;

use App\Administrator;
use Illuminate\Console\Command;

class MakeAdministrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates an administrator with given email and password. If this email is already existing, sets it's password to a given value.";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = \HUser::normalizeEmail($this->argument('email'));
        $password = $this->argument('password');

        /**
         * @var Administrator?
         */
        $administrator = Administrator::where('email', '=', $email)->first();

        if (null === $administrator) {
            $administrator = new Administrator();
            $administrator->email = $email;
            $administrator->role = Administrator::ROLE_ADMIN;
            $administrator->setPassword($password);
        } else {
            $administrator->setPassword($password);
        }

        $administrator->save();
    }
}
