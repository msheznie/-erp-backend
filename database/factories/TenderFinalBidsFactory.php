<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderFinalBids;
use Faker\Generator as Faker;

$factory->define(TenderFinalBids::class, function (Faker $faker) {

    return [
        'award' => $faker->word,
        'bid_id' => $faker->randomDigitNotNull,
        'com_weightage' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'status' => $faker->word,
        'supplier_id' => $faker->randomDigitNotNull,
        'tech_weightage' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'total_weightage' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
