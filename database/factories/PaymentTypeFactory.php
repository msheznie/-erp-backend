<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentType;
use Faker\Generator as Faker;

$factory->define(PaymentType::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
