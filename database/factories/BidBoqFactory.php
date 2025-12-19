<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BidBoq;
use Faker\Generator as Faker;

$factory->define(BidBoq::class, function (Faker $faker) {

    return [
        'boq_id' => $faker->randomDigitNotNull,
        'bid_master_id' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'unit_amount' => $faker->randomDigitNotNull,
        'total_amount' => $faker->randomDigitNotNull,
        'remarks' => $faker->word,
        'supplier_registration_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
