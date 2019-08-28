<?php

use App\Administrator;
use App\Services\UserService;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Administrator::class, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');

    return [
        'name' => "{$faker->firstName} {$faker->lastName}",
        'email' => $faker->email,
        'password' => app(UserService::class)->getPasswordHash($faker->password),
        'role' => Administrator::ROLE_PARTNER,
        'api_token' => Str::random(),
        'partner_id' => \Registry::lazyPartnerId(),
        'created_at' => $createdAt,
        'updated_at' => $faker->dateTimeBetween($createdAt),
        'last_visited_at' => $faker->dateTimeBetween($createdAt),
    ];
});

$factory->state(Administrator::class, 'main', [
    'is_main' => true,
]);
