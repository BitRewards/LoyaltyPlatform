<?php

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* @var Factory $factory */
$factory->define(Notification::class, function (Faker $faker) use ($factory) {
    static $user;

    if (!$user) {
        $user = $factory->create(User::class);
    }

    return [
        'type' => Notification::TYPE_UNSPENT_BALANCE_REMINDER_FIRST,
        'text' => $faker->text,
        'user_id' => $user->id,
        'created_at' => Carbon::now()->subDay(),
        'updated_at' => Carbon::now()->subHour(),
    ];
});
