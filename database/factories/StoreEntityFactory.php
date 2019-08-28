<?php

use App\Models\StoreEntity;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(StoreEntity::class, function (Faker $faker) {
    return factory(StoreEntity::class, StoreEntity::TYPE_ORDER)->raw();
});

$factory->defineAs(StoreEntity::class, StoreEntity::TYPE_ORDER, function (Faker $faker) {
    $createdAt = $faker->dateTimeBetween('-1 year');

    return [
        'type' => StoreEntity::TYPE_ORDER,
        'partner_id' => \Registry::lazyPartnerId(),
        'status' => StoreEntity::STATUS_PENDING,
        'created_at' => $createdAt,
        'updated_at' => $faker->dateTimeBetween($createdAt),
        'data' => [
            'amountTotal' => 100,
        ],
    ];
});

$factory->state(StoreEntity::class, StoreEntity::STATUS_PENDING, [
    'status' => StoreEntity::STATUS_PENDING,
]);

$factory->state(StoreEntity::class, StoreEntity::STATUS_CONFIRMED, function (Faker $faker) use ($factory) {
    $raw = $factory->raw(StoreEntity::class);

    return [
        'status' => StoreEntity::STATUS_CONFIRMED,
        'confirmed_at' => $faker->dateTimeBetween('-1 year'),
        'data' => [
            'isPaid' => true,
        ] + $raw['data'],
    ];
});

$factory->state(StoreEntity::class, StoreEntity::STATUS_REJECTED, function (Faker $faker) {
    return [
        'status' => StoreEntity::STATUS_REJECTED,
        'rejected_at' => $faker->dateTimeBetween('-1 year'),
    ];
});
