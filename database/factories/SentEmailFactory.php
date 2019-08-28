<?php

use App\Models\SentEmail;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SentEmail::class, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');

    return [
        'created_at' => $createdAt,
        'updated_at' => $faker->dateTimeBetween($createdAt),
        'email' => $faker->email,
        'subject' => $faker->realText(100),
        'body' => $faker->realText(255),
        'token' => Str::random(32),
    ];
});
