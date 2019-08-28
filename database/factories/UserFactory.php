<?php

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/* @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');

    return [
        'key' => Str::random(),
        'name' => "{$faker->firstName} {$faker->lastName}",
        'balance' => 100,
        'signup_type' => User::SIGNUP_TYPE_ORGANIC,
        'person_id' => \Registry::lazyPersonId(),
        'partner_id' => \Registry::lazyPartnerId(),
        'emails_received' => [],
        'is_unsubscribed' => false,
        'created_at' => $createdAt,
        'updated_at' => $faker->dateTimeBetween($createdAt),
    ];
});

$factory->defineAs(User::class, 'use_email', function (Faker $faker) {
    $data = factory(User::class)->raw([
        'email' => $faker->email,
    ]);

    return [
        'email_confirmed_at' => $faker->dateTimeBetween($data['created_at']),
    ] + $data;
});

$factory->defineAs(User::class, 'use_phone', function (Faker $faker) {
    $data = factory(User::class)->raw([
        'phone' => $faker->phoneNumber,
    ]);

    return [
        'phone_confirmed_at' => $faker->dateTimeBetween($data['created_at']),
    ] + $data;
});

$factory->state(User::class, 'not_confirmed', function (Faker $faker) {
    return [
        'email_confirmed_at' => null,
        'phone_confirmed_at' => null,
    ];
});
