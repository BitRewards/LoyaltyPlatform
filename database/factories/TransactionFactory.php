<?php

use App\Models\Transaction;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Transaction::class, function (Faker $faker) {
    return factory(Transaction::class, Transaction::TYPE_ACTION)->raw();
});

$factory->defineAs(Transaction::class, Transaction::TYPE_ACTION, function (Faker $faker) {
    return [
        'balance_change' => 100,
        'status' => Transaction::STATUS_PENDING,
        'user_id' => \Registry::lazyUserId(),
        'partner_id' => \Registry::lazyPartnerId(),
        'type' => Transaction::TYPE_ACTION,
        'action_id' => \Registry::lazyActionId(),
    ];
});

$factory->defineAs(Transaction::class, Transaction::TYPE_REWARD, function (Faker $faker) {
    return [
        'balance_change' => -100,
        'status' => Transaction::STATUS_PENDING,
        'user_id' => \Registry::lazyUserId(),
        'partner_id' => \Registry::lazyPartnerId(),
        'type' => Transaction::TYPE_REWARD,
        'reward_id' => \Registry::lazyRewardId(),
    ];
});

$factory->state(Transaction::class, Transaction::STATUS_PENDING, [
    'status' => Transaction::STATUS_PENDING,
]);

$factory->state(Transaction::class, Transaction::STATUS_CONFIRMED, [
    'status' => Transaction::STATUS_CONFIRMED,
]);

$factory->state(Transaction::class, Transaction::STATUS_REJECTED, [
    'status' => Transaction::STATUS_REJECTED,
]);
