<?php

use App\Models\Person;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Person::class, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');
    $lastVisitedAt = $faker->dateTimeBetween($createdAt);

    return [
        'partner_group_id' => \Registry::lazyPartnerGroupId(),
        'remember_token' => Str::random(),
        'created_at' => $createdAt,
        'updated_at' => $lastVisitedAt,
        'last_visited_at' => $lastVisitedAt,
    ];
});
