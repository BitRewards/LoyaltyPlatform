<?php

use App\Services\UserService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function run(): void
    {
        $this->call([
            TestPartnerSeeder::class,
        ]);
    }
}
