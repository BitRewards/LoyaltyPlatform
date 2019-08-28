<?php

namespace App\Console\Commands\User;

use App\Administrator;
use Illuminate\Console\Command;

class FixAdministratorsEmptyApiToken extends Command
{
    protected $signature = 'FixAdministratorsEmptyApiToken';

    protected $description = '';

    public function handle()
    {
        $admins = Administrator::query()->whereNull('api_token')->get();

        foreach ($admins as $admin) {
            /*
             * @var Administrator $admin
             */
            $admin->api_token = str_random(16);
            $admin->save();
        }
    }
}
