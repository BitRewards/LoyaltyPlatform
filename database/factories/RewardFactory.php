<?php

use App\Models\Reward;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Reward::class, function (Faker $faker) {
    return [
        'partner_id' => \Registry::lazyPartnerId(),
        'type' => Reward::TYPE_GIFTD_DISCOUNT,
        'price_type' => Reward::PRICE_TYPE_POINTS,
        'price' => 100,
        'value' => 5,
        'status' => Reward::STATUS_ENABLED,
    ];
});

$factory->defineAs(Reward::class, Reward::TYPE_GIFTD_DISCOUNT, function () {
    return factory(Reward::class)->raw([
        'type' => Reward::TYPE_GIFTD_DISCOUNT,
    ]);
});

$factory->defineAs(Reward::class, Reward::TYPE_BITREWARDS_PAYOUT, function () {
    return factory(Reward::class)->raw([
        'type' => Reward::TYPE_BITREWARDS_PAYOUT,
    ]);
});

$factory->defineAs(Reward::class, Reward::TYPE_FIAT_WITHDRAW, function () {
    return factory(Reward::class)->raw([
        'type' => Reward::TYPE_FIAT_WITHDRAW,
    ]);
});

$factory->state(Reward::class, 'disabled', [
    'status' => Reward::STATUS_DISABLED,
]);
