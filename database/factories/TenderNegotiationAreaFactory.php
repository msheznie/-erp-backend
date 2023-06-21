<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderNegotiationArea;
use Faker\Generator as Faker;

$factory->define(TenderNegotiationArea::class, function (Faker $faker) {

    return [
        'tender_negotiation_id' => $faker->randomDigitNotNull,
        'area' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
