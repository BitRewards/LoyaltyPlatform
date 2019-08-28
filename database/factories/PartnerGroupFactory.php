<?php

use App\Models\PartnerGroup;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(PartnerGroup::class, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');

    return [
        'name' => $faker->company,
        'created_at' => $createdAt,
        'updated_at' => $faker->dateTimeBetween($createdAt),
    ];
});
