<?php

namespace App\Console\Commands\Faker;

use App\Models\Partner;
use App\Services\FakeDataService;
use Illuminate\Console\Command;

class CreateUsers extends Command
{
    protected $signature = 'faker:users {partner_id}';

    public function handle()
    {
        $partner = Partner::whereId($this->argument('partner_id'))->first();

        if (!$partner) {
            throw new \InvalidArgumentException('Partner not found');
        }

        $this->populatePartnerWithFakeUsers($partner, mt_rand(800, 1200));
    }

    private function populatePartnerWithFakeUsers(Partner $partner, $count = 1)
    {
        $this->info("Creating {$count} fake users for {$partner->title}");

        $faker = app(FakeDataService::class);

        while ($count--) {
            $user = $faker->createUserForPartner($partner);
            $faker->createTransactionsForUser($user, mt_rand(2, 10));
        }
    }
}
